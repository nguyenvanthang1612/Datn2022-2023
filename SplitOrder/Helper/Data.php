<?php

namespace Magenest\SplitOrder\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check if module is active.
     *
     * @param int $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'api_config/module/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get attributes to split.
     *
     * @param int $storeId
     * @return string
     */
    public function getAttributes($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'api_config/options/attributes',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should split delivery.
     *
     * @param string $storeId
     * @return bool
     */
    public function getShippingSplit($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'api_config/options/shipping',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get which kind of attribute related with qty should be load.
     *
     * @param int $storeId
     * @return bool
     */
    public function getQtyType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'api_config/options/attribute_qty',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * If should apply out of stock if inventory is empty.
     *
     * @param int $storeId
     * @return string
     */
    public function getBackorder($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'api_config/options/qty_backorder',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
