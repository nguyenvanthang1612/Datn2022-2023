<?php


namespace Magenest\GiaoHangNhanh\Observer;


use Magento\Framework\Event\ObserverInterface;

class CancelGhnObserver implements ObserverInterface
{
    /**
     * @var \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh
     */
    protected $shippingMethod;

    /**
     * SalesOrderShipmentAfter constructor.
     * @param \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh $shippingMethod
     */
    public function __construct(
        \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh $shippingMethod
    ) {
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder()->getData('ghn_order_code_attribute');
        $orderShippingMethod = $observer->getEvent()->getOrder()->getData('shipping_method');
        if ($orderShippingMethod == 'giaohangnhanh_giaohangnhanh' && $order != '') {
            $this->shippingMethod->cancelOrder($order);
        }
    }
}
