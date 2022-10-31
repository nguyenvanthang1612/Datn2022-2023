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

namespace Magenest\RegionStock\Block\Product\Widget;

use Magenest\RegionStock\Plugin\Magento\AppendCacheKeyPlugin;

/**
 * Class NewWidget
 *
 * @package Magenest\RegionStock\Block\Product\Widget
 */
class NewWidget extends \Magento\Catalog\Block\Product\Widget\NewWidget
{
    /**
     * @var AppendCacheKeyPlugin
     */
    protected $_appendCacheKeyPlugin;

    /**
     * NewWidget constructor.
     *
     * @param AppendCacheKeyPlugin                                           $appendCacheKeyPlugin
     * @param \Magento\Catalog\Block\Product\Context                         $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context                            $httpContext
     * @param array                                                          $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null              $serializer
     */
    public function __construct(
        AppendCacheKeyPlugin $appendCacheKeyPlugin,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_appendCacheKeyPlugin = $appendCacheKeyPlugin;
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $data, $serializer);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = parent::_getProductCollection();
        $this->_appendCacheKeyPlugin->addRegionFilter($collection);

        return $collection;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeys = parent::getCacheKeyInfo();
        $this->_appendCacheKeyPlugin->append($cacheKeys);

        return $cacheKeys;
    }

    protected function _beforeToHtml()
    {
        $object = parent::_beforeToHtml();
        $object->setTemplate('Magento_Catalog::product/widget/new/content/new_grid.phtml');

        return $object;
    }
}
