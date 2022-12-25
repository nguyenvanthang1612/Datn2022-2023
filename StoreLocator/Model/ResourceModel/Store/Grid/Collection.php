<?php

namespace Magenest\StoreLocator\Model\ResourceModel\Store\Grid;

class Collection extends \Magenest\StoreLocator\Model\ResourceModel\Store\Collection
{
    /**
     * Sets flag for customer info loading on load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        //$this->showCustomerInfo(true)->addSubscriberTypeField()->showStoreInfo();
        return $this;
    }
}
