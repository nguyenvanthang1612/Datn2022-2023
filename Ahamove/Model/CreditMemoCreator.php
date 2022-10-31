<?php

namespace Magenest\Ahamove\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class CreditMemoCreator
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender,
        ObjectManagerInterface $objectManager,
        \Magenest\Ahamove\Helper\OrderHelper $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoSender = $creditmemoSender;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @param $reason
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function start($order, $reason)
    {
        $creditMemo = [
            'do_offline' => '1',
            'comment_text' => $reason,
            'shipping_amount' => $order->getBaseShippingAmount(),
            'adjustment_positive' => 0,
            'adjustment_negative' => 0
        ];
        $items = [];
        foreach ($order->getAllItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                continue;
            }
            if ($item->getParentItemId()) {
                $items[$item->getParentItemId()]['qty'] = $item->getQtyOrdered();
                $items[$item->getParentItemId()]['back_to_stock'] = '1';
                continue;
            }
            $items[$item->getId()]['qty'] = (int)$item->getQtyOrdered();
            $items[$item->getId()]['back_to_stock'] = '1';
        }
        $creditMemo['items'] = $items;
        $this->create($order->getId(), $creditMemo);
    }

    /**
     * @param $orderId
     * @param $creditMemo
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function create($orderId, $creditMemo)
    {
        $this->creditmemoLoader->setOrderId($orderId);
        $this->creditmemoLoader->setCreditmemo($creditMemo);
        $creditMemo = $this->creditmemoLoader->load();
        if ($creditMemo) {
            if (!$creditMemo->isValidGrandTotal()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The credit memo\'s total must be positive.')
                );
            }

            if (!empty($creditMemo['comment_text'])) {
                $creditMemo->addComment(
                    $creditMemo['comment_text'],
                    isset($creditMemo['comment_customer_notify']),
                    isset($creditMemo['is_visible_on_front'])
                );

                $creditMemo->setCustomerNote($creditMemo['comment_text']);
                $creditMemo->setCustomerNoteNotify(isset($creditMemo['comment_customer_notify']));
            }

            if (isset($creditmemo['do_offline'])) {
                //do not allow online refund for Refund to Store Credit
                if (!$creditMemo['do_offline'] && !empty($creditMemo['refund_customerbalance_return_enable'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Cannot create online refund for Refund to Store Credit.')
                    );
                }
            }
            $creditMemoManagement = $this->_objectManager->create(
                \Magento\Sales\Api\CreditmemoManagementInterface::class
            );
            $creditMemo->getOrder()->setCustomerNoteNotify(!empty($creditMemo['send_email']));
            $doOffline = isset($creditmemo['do_offline']) ? (bool)$creditMemo['do_offline'] : false;
            $creditMemoManagement->refund($creditMemo, $doOffline);

            try {
                if (!empty($creditMemo['send_email'])) {
                    $this->creditmemoSender->send($creditMemo);
                }
            } catch (\Exception $e) {
                $this->helper->debug($e);
            }
        }
    }
}
