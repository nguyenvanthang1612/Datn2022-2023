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

namespace Magenest\RegionStock\Plugin\Magenest\FlashSale\Block;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

class WidgetPlugin extends AppendCacheKeyPlugin
{
    public function afterGetCacheKeyInfo(\Magenest\FlashSale\Block\Widget $subject, $cacheKeys)
    {
        return $this->append($cacheKeys);
    }
}
