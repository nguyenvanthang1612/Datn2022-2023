<?php

namespace Magenest\TrackingOrder\Block;

class KsOrderTotals extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var Order|null
     */
    protected $_order = null;

    /**
     * Get order object
     *
     * @return Order
     */
    public function getOrder()
    {
        if ($this->_order === null) {
            $this->_order = $this->_coreRegistry->registry('orderdata');
        }
        return $this->_order;
    }
}
