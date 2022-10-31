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

namespace Magenest\RegionStock\Plugin\Magenest\SwatchColorSplit\Block\Swatches;

use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;

class Color
{
    protected $helper;

    protected $getRegionSalableQty;
    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    public function __construct(
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        \Magenest\RegionStock\Helper\Helper $helper,
        \Magenest\RegionStock\Api\GetRegionSalableQtyInterface $getRegionSalableQty
    ) {
        $this->helper = $helper;
        $this->getRegionSalableQty = $getRegionSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
    }

    public function afterIsSalable(\Magenest\SwatchColorSplit\Block\Swatches\Color $subject, $result, $product)
    {
        $stockId = $this->getStockIdForCurrentWebsite->execute();
        $cookie = $this->helper->getCookie();
        $regionStock = true;
        if ($this->helper->isEnableRegionStock() && $cookie) {
            try {
                $regionQty = (float)$this->getRegionSalableQty->execute($product->getSku(), $cookie, $stockId);
                $regionStock = $regionQty > 0;
            } catch (\Throwable $e) {
                $this->helper->debug($e);
            }
        }

        return $result && $regionStock;
    }
}
