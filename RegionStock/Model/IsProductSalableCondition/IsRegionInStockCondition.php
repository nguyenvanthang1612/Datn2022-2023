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

namespace Magenest\RegionStock\Model\IsProductSalableCondition;

use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForSkuInterface;

class IsRegionInStockCondition implements IsProductSalableInterface
{
    /**
     * @var ManageStockCondition
     */
    private $manageStockCondition;

    /**
     * @var IsSourceItemManagementAllowedForSkuInterface
     */
    private $isSourceItemManagementAllowedForSku;

    protected $helper;

    protected $getRegionSalableQty;

    public function __construct(
        ManageStockCondition $manageStockCondition,
        IsSourceItemManagementAllowedForSkuInterface $isSourceItemManagementAllowedForSku,
        \Magenest\RegionStock\Helper\Helper $helper,
        \Magenest\RegionStock\Api\GetRegionSalableQtyInterface $getRegionSalableQty
    ) {
        $this->helper = $helper;
        $this->getRegionSalableQty = $getRegionSalableQty;
        $this->manageStockCondition = $manageStockCondition;
        $this->isSourceItemManagementAllowedForSku = $isSourceItemManagementAllowedForSku;
    }

    public function execute(string $sku, int $stockId): bool
    {
        if (!$this->helper->isEnableRegionStock() || $this->manageStockCondition->execute($sku, $stockId) || !$this->isSourceItemManagementAllowedForSku->execute($sku)) {
            return true;
        }

        $cookie = $this->helper->getCookie();
        if ($cookie) {
            try {
                $regionQty = (float)$this->getRegionSalableQty->execute($sku, $cookie, $stockId);

                return $regionQty > 0;
            } catch (\Throwable $e) {
                $this->helper->debug($e);
            }
        }

        return true;
    }
}
