<?php

namespace Magenest\GiaoHangNhanh\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
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
        $shipment = $observer->getEvent()->getShipment();
        $result = new \Magento\Framework\DataObject();
        $result->setTrackingNumber($shipment->getOrderId());
        $result->setShippingLabelContent('GHN Service');
        $weight = 10;
        $length = 10;
        $width = 10;
        $height = 10;
        $type = 2;
        if ($shipment->getOrder()->getData('shipping_method') == 'giaohangnhanh_giaohangnhanh') {
            $this->shippingMethod->createOrder($shipment, $weight, $length, $width, $height, $type, $result);
        }
    }
}
