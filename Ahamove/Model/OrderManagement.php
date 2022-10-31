<?php


namespace Magenest\Ahamove\Model;

use Magenest\Ahamove\Helper\Helper;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;

class OrderManagement
{
    const STATUS_PENDING = 'pending';
    const STATUS_ORDER_CONFIRM = 'order_confirm';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPMENT_PROCESSING = 'shipment_processing';
    const STATUS_SHIPMENT_COMPLETE = 'shipment_delivered';
    const STATUS_ORDER_RETURNED = 'order_returned';
    const STATUS_RETURN_REJECTED = 'return_rejected';
    const STATUS_RETURN_ACCEPTED = 'return_accepted';
    const STATUS_RETURN_COMPLETED = 'return_completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_CANCELED_BY_ADMIN = 'canceled_by_admin';
    const STATUS_CANCELED_BY_BUYER = 'canceled_by_buyer';
    const STATUS_COMPLETE = 'complete';
    const STATUS_HOLDED = 'holded';
    const STATUS_CLOSED = 'closed';
    const STATUS_DELIVERY_FAILED = 'delivery_failed';
    const STATUS_BUYER_REFUND_COMPLETED = 'buyer_refund_completed';
    const STATUS_ADMIN_REFUND_COMPLETED = 'admin_refund_completed';

    const AUTOMATICALLY_SEND_EMAIL_CONFIG = "sales_email/order/cancel_email";
    const CANCEL_BY_ADMIN = 'admin';
    const CANCEL_BY_BUYER = 'buyer';

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var CreditmemoCreator
     */
    protected $creditMemoCreator;

    /**
     * @var OrderCommentSender
     */
    protected $orderCommentSender;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * OrderManagement constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param ManagerInterface $eventManager
     * @param CreditMemoCreator $creditMemoCreator
     * @param OrderCommentSender $orderCommentSender
     * @param Helper $helper
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $eventManager,
        \Magenest\Ahamove\Model\CreditMemoCreator $creditMemoCreator,
        OrderCommentSender $orderCommentSender,
        Helper $helper,
        InvoiceService $invoiceService,
        Transaction $transaction
    ) {
        $this->orderRepository = $orderRepository;
        $this->_eventManager = $eventManager;
        $this->creditMemoCreator = $creditMemoCreator;
        $this->orderCommentSender = $orderCommentSender;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->helper = $helper;
    }

    public function cancelOrder(\Magento\Sales\Model\Order $order, $reason = null, $cancelBy = null, $sendMail = false)
    {
        if ($order->canCancel()) {
            $order->cancel();
            if ($cancelBy == self::CANCEL_BY_ADMIN) {
                $order->setStatus(self::STATUS_CANCELED_BY_ADMIN);
            } elseif ($cancelBy == self::CANCEL_BY_BUYER) {
                $order->setStatus(self::STATUS_CANCELED_BY_BUYER);
            }
            $order->setCancelledReason($reason);
            $this->orderRepository->save($order);
        } elseif (!$order->isCanceled() && $order->hasInvoices() && !$order->canCancel() && !$order->hasShipments()) {
            // cancel order paid by online payment
            $originOrder = $this->orderRepository->get($order->getId());
            if ($originOrder->getInvoiceCollection()->count()) {
                /** @var Invoice $invoice */
                foreach ($originOrder->getInvoiceCollection() as $invoice) {
                    $invoice->setState(Invoice::STATE_OPEN); // Force cancel invoice
                    if ($invoice->canCancel()) {
                        $invoice->cancel();
                        $invoice->save();
                    }
                }
            }
            $originOrder->cancel();
            if ($cancelBy == self::CANCEL_BY_ADMIN) {
                $originOrder->setStatus(self::STATUS_CANCELED_BY_ADMIN);
            } elseif ($cancelBy == self::CANCEL_BY_BUYER) {
                $originOrder->setStatus(self::STATUS_CANCELED_BY_BUYER);
            }
            $originOrder->setCancelledReason($reason);
            $this->orderRepository->save($originOrder);
        } elseif ($order->hasShipments() && !$order->canCancel() && $order->canCreditmemo()) {
            $isCreditMemo = true;
            $this->creditMemoCreator->start($order, $reason);
        }
        if ($reason && !isset($isCreditMemo)) {
            $this->_eventManager->dispatch(
                "order_action_save_comment_history",
                [
                    'order' => $order,
                    'comment' => __("Order have been cancelled. Reason: %1", $reason)
                ]
            );
        }
        if ($sendMail && $this->helper->getStoreConfig(self::AUTOMATICALLY_SEND_EMAIL_CONFIG)) {
            $message = __("Your order has been cancelled. ");
            $message = isset($reason) && $reason ? $reason : $message;
            $this->orderCommentSender->send($order, true, $message);
        }

        return false;
    }

    public function ahamoveStatus(Order $order, $shipmentStatus)
    {
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("Shipment has been updated with status: %1", $shipmentStatus)
        ]);
    }

    public function shipmentFailed(Order $order)
    {
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(self::STATUS_DELIVERY_FAILED);
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("Order delivered have been failed.")
        ]);
    }

    public function confirmStock(Order $order)
    {
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(self::STATUS_ORDER_CONFIRM);
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("Order have been confirmed stock sufficient.")
        ]);
        $this->orderRepository->save($order);
    }

    /**
     * @param Order $order
     */
    public function confirmDelivered(Order $order)
    {
        if ($order->getPayment()
            && $order->getPayment()->getMethod() == \Magento\OfflinePayments\Model\Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE) {
            $this->createInvoice($order);
            $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE)->setStatus(self::STATUS_COMPLETE);
        } else {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(self::STATUS_SHIPMENT_COMPLETE);
        }
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("Order have been confirmed order delivered success.")
        ]);
        $this->orderRepository->save($order);
    }

    public function returnOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(self::STATUS_ORDER_RETURNED);
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("Order have been returned by 3PL.")
        ]);
        $this->orderRepository->save($order);
    }

    public function approveReturn(Order $order)
    {
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(self::STATUS_RETURN_ACCEPTED);
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("RMA request have been approved.")
        ]);
        if ($order->canInvoice()) {
            $order->setState(\Magento\Sales\Model\Order::STATE_CLOSED)->setStatus(self::STATUS_RETURN_COMPLETED);
        }
        $this->orderRepository->save($order);
    }

    public function rejectReturn(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE)->setStatus(self::STATUS_RETURN_REJECTED);
        $this->_eventManager->dispatch("order_action_save_comment_history", [
            'order' => $order,
            'comment' => __("RMA request have been rejected.")
        ]);
        $this->orderRepository->save($order);
    }

    public function createInvoice(Order $order)
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->_eventManager->dispatch('order_action_save_comment_history', [
                'order' => $order,
                'comment' => __('Invoice is auto generated after create exchange order', $invoice->getId())
            ]);
            $this->orderRepository->save($order);
        }
    }

    public function refundCompleted(Order $order)
    {
        if ($order->getStatus() == self::STATUS_CANCELED_BY_BUYER) {
            $order->setStatus(self::STATUS_BUYER_REFUND_COMPLETED);
            $this->_eventManager->dispatch("order_action_save_comment_history", [
                'order' => $order,
                'comment' => __("The refund has been completed by buyer.")
            ]);
        } else {
            $order->setStatus(self::STATUS_ADMIN_REFUND_COMPLETED);
            $this->_eventManager->dispatch("order_action_save_comment_history", [
                'order' => $order,
                'comment' => __("The refund has been completed by admin.")
            ]);
        }
        $this->orderRepository->save($order);
    }
}
