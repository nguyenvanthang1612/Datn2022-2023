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

namespace Magenest\RegionStock\Plugin\Magento\Catalog\Block\Product\ProductList;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

class Toolbar extends AppendCacheKeyPlugin
{
    private $isAdded = false;

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return mixed
     */
    public function beforeSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        $collection
    ) {
        if (!$this->isAdded) {
            $collection = $this->addRegionFilter($collection);
        }
        $this->isAdded = true;

        return [$collection];
    }
}
