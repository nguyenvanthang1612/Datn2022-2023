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

namespace Magenest\RegionStock\Plugin\Magenest\Widget\Block\Product\Widget;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

class ProductsPlugin extends AppendCacheKeyPlugin
{
    public function afterGetCacheKeyInfo(\Magenest\Widget\Block\Product\Widget\Products $subject, $cacheKeys)
    {
        return $this->append($cacheKeys);
    }

    public function afterGetProductCollection(\Magenest\Widget\Block\Product\Widget\Products $subject, $collection)
    {
        return $this->addRegionFilter($collection);
    }
}
