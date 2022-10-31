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

namespace Magenest\RegionStock\Plugin\Magenest\SwatchColorSplit\Model\Plugin\Frontend;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class UsedProductsCachePlugin extends AppendCacheKeyPlugin
{
    public function beforeAroundGetUsedProductsWithoutStock(
        \Magenest\SwatchColorSplit\Model\Plugin\Frontend\UsedProductsCache $cache,
        \Magenest\SwatchColorSplit\Model\Product\Type\ConfigurableMedia $subject,
        callable $proceed,
        $product,
        $requiredAttributeIds = null
    ) {
        $cookie = $this->helper->getCookie();
        if ($this->helper->isEnableRegionStock() && isset(self::MAPPING_REGION_ATTRIBUTE[$cookie])) {
            if (!empty($requiredAttributeIds) && is_array($requiredAttributeIds)) {
                $requiredAttributeIds[] = self::MAPPING_REGION_ATTRIBUTE[$cookie];
            } else {
                $requiredAttributeIds = [self::MAPPING_REGION_ATTRIBUTE[$cookie]];
            }
        }

        return [$subject, $proceed, $product, $requiredAttributeIds];
    }
}
