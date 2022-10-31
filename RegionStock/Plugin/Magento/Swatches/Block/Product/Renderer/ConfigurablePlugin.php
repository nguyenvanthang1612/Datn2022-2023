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

namespace Magenest\RegionStock\Plugin\Magento\Swatches\Block\Product\Renderer;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

class ConfigurablePlugin extends AppendCacheKeyPlugin
{
    public function afterGetCacheKey(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $cacheKey)
    {
        return $this->append($cacheKey, '-');
    }
}
