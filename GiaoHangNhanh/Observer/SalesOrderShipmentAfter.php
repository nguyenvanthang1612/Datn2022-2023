<?php

namespace Magenest\GiaoHangNhanh\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
{
    protected $shippingMethod;

    public function __construct(
        \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh $shippingMethod
    ) {
        $this->shippingMethod = $shippingMethod;
    }

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
        $this->shippingMethod->createOrder($shipment, $weight, $length, $width, $height, $type, $result);

    }
}
