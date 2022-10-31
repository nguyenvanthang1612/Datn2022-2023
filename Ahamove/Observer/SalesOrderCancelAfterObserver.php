<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 26/02/2020
 * Time: 08:31
 */

namespace Magenest\Ahamove\Observer;


use Magenest\Ahamove\Helper\ShipmentHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\Ahamove\Model\Carrier\Ahamove;

/**
 * Class SalesOrderCancelAfterObserver
 * @package Magenest\Ahamove\Observer
 */
class SalesOrderCancelAfterObserver implements ObserverInterface
{
    /**
     * @var Ahamove
     */
    protected $ahamoveCarrier;
    /**
     * @var ShipmentHelper
     */
    protected $shipmentHelper;

    /**
     * SalesOrderCancelAfterObserver constructor.
     *
     * @param Ahamove              $ahamoveCarrier
     * @param ShipmentHelper       $shipmentHelper
     */
    public function __construct(
        Ahamove $ahamoveCarrier,
        ShipmentHelper $shipmentHelper
    ){
        $this->ahamoveCarrier = $ahamoveCarrier;
        $this->shipmentHelper = $shipmentHelper;
    }

    /**
     * @param Observer $observer
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            $order = $observer->getEvent()->getPayment()->getOrder();
        }
        if($order && $order->getApiOrderId()){
            if (strpos($order->getShippingMethod(), 'ahamove') !== false) {
                $params = [
                    'token' => $this->shipmentHelper->getCarrierApiToken(),
                    'order_id' => $order->getApiOrderId(),
                    'comment' => ''
                ];
                $this->ahamoveCarrier->cancelOrder($params);
            }
        }
    }
}
