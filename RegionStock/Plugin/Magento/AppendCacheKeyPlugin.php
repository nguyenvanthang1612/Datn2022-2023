<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Richs extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Richs
 */

namespace Magenest\RegionStock\Plugin\Magento;

use Magenest\RegionStock\Model\OptionSource\RegionSalable;

class AppendCacheKeyPlugin
{
    const MAPPING_REGION_ATTRIBUTE = [
        RegionSalable::HANOI_SALABLE_ATTR => -1,
        RegionSalable::DANANG_SALABLE_ATTR => -2,
        RegionSalable::HCMC_SALABLE_ATTR => -3,
        RegionSalable::CANTHO_SALABLE_ATTR => -4,
    ];

    protected $helper;

    public function __construct(
        \Magenest\RegionStock\Helper\Helper $helper
    ) {
        $this->helper = $helper;
    }

    public function append($cacheKeys, $prefix = '')
    {
        if ($this->helper->isEnableRegionStock()) {
            $cookie = $this->helper->getCookie();
            if (is_array($cacheKeys)) {
                $cacheKeys[] = $cookie;
            } elseif (is_string($cacheKeys)) {
                $cacheKeys .= $prefix . $cookie;
            }
        }

        return $cacheKeys;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function addRegionFilter($collection)
    {
        $cookie = $this->helper->getCookie();
        $regionSalable = $this->helper->getRegionSalableConfig($cookie);
        if ($this->helper->isEnableRegionStock() && $regionSalable && !$this->helper->isDisplayOutOfStockProduct()) {
            $collection->addFieldToFilter($regionSalable, 1);
        }

        return $collection;
    }
}
