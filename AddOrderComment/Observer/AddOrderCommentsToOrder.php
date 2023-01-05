<?php
namespace Magenest\AddOrderComment\Observer;

class AddOrderCommentsToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setData('order_comments', $quote->getOrderComments());
    }
}
