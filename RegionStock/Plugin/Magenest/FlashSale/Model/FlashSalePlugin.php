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

namespace Magenest\RegionStock\Plugin\Magenest\FlashSale\Model;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

class FlashSalePlugin extends AppendCacheKeyPlugin
{
    public function afterGetProductCollection(\Magenest\FlashSale\Model\FlashSale $subject, $collection)
    {
        return $this->addRegionFilter($collection);
    }
}
