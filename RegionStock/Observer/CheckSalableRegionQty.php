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

namespace Magenest\RegionStock\Observer;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;

class CheckSalableRegionQty implements ObserverInterface
{
    protected $helper;

    protected $getRegionSalableQty;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    private $stockId = null;

    public function __construct(
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        \Magenest\RegionStock\Helper\Helper $helper,
        \Magenest\RegionStock\Api\GetRegionSalableQtyInterface $getRegionSalableQty
    ) {
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->helper = $helper;
        $this->getRegionSalableQty = $getRegionSalableQty;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        if ($product->getTypeId() != BundleType::TYPE_CODE) {
            $cookie = $this->helper->getCookie();
            if ($this->helper->isEnableRegionStock() && $cookie) {
                $this->stockId = $this->getStockIdForCurrentWebsite->execute();
                $regionQty = (float)$this->getRegionSalableQty->execute($product->getSku(), $cookie, $this->stockId);
                $observer->getEvent()->getSalable()->setIsSalable($regionQty > 0);
            }
        }
        return $this;
    }
}
