<?php


namespace Magenest\StoreLocator\Observer;


class SaveToOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getQuote();
        $order = $event->getOrder();
        $order->setData('store_name_list', $quote->getData('store_name_list'));
    }
}
