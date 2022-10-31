<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\RegionStock\Plugin\Magento\Catalog\Block\Product;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

/**
 * Catalog Products List widget block plugin
 */
class ProductsListPlugin extends AppendCacheKeyPlugin
{
    /**
     * @param \Magento\CatalogWidget\Block\Product\ProductsList $subject
     * @param array $cacheKeys
     * @return array
     */
    public function afterGetCacheKeyInfo(\Magento\CatalogWidget\Block\Product\ProductsList $subject, array $cacheKeys)
    {
        return $this->append($cacheKeys);
    }

    public function afterCreateCollection(
        \Magento\CatalogWidget\Block\Product\ProductsList $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $result
    ) {
        return $this->addRegionFilter($result);
    }
}
