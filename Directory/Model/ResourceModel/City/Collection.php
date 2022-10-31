<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\ResourceModel\City;

use Magenest\Directory\Model\ResourceModel\AbstractCollection;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Collection
 * @package Magenest\Directory\Model\ResourceModel\City
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'city_id';

    /**
     * {@inheritdoc}
     */
    protected $_foreignKey = 'country_id';

    /**
     * {@inheritdoc}
     */
    protected $_defaultOptionLabel = 'Please select city';

    /**
     * {@inheritdoc}
     */
    protected $_defaultValue = 'VN';

    /**
     * {@inheritdoc}
     */
    protected $_sortable = true;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\Directory\Model\City::class, \Magenest\Directory\Model\ResourceModel\City::class);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareOptionArray()
    {
        return parent::prepareOptionArray();
    }

    public function storefrontToOptionArray()
    {
        $arr = parent::toOptionArray();
       foreach ($arr as $key => $value) {
           $disabled = !empty($value['disable_on_storefront']) ? (bool)$value['disable_on_storefront'] : false;
           if ($disabled || empty($value['value'])) {
               unset($arr[$key]);
           }
       }
       return $arr;
    }
}
