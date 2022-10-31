<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Project extension
 * NOTICE OF LICENSE
 *
 * @author   PhongNguyen
 * @category Magenest
 * @package  Magenest_Project
 */
namespace Magenest\RegionStock\Plugin\Mageplaza\Productslider\Block;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

/**
 * Class CategoryId
 *
 * @package Magenest\RegionStock\Plugin\Mageplaza\Productslider\Block
 */
class CategoryId extends AppendCacheKeyPlugin
{
    /**
     * @param \Mageplaza\Productslider\Block\CategoryId $subject
     * @param array                                     $cacheKeys
     *
     * @return array
     */
    public function afterGetCacheKeyInfo(\Mageplaza\Productslider\Block\CategoryId $subject, array $cacheKeys)
    {
        return $this->append($cacheKeys);
    }

    /**
     * @param \Mageplaza\Productslider\Block\CategoryId $subject
     * @param                                           $result
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function afterGetProductCollection(
        \Mageplaza\Productslider\Block\CategoryId $subject,
        $result
    ) {
        if (!empty($result)) {
            return $this->addRegionFilter($result);
        }
        return $result;
    }
}
