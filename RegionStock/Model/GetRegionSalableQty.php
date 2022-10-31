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

namespace Magenest\RegionStock\Model;

use Magenest\RegionPopup\Model\OptionSource\Region;
use Magenest\RegionStock\Api\GetRegionSalableQtyInterface;
use Magenest\RegionStockIndexer\Indexer\IndexStructure;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

class GetRegionSalableQty implements GetRegionSalableQtyInterface
{
    protected $helper;

    protected $storeManager;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;

    public function __construct(
        ResourceConnection $resource,
        \Magenest\RegionStock\Helper\Helper $helper,
        DefaultStockProviderInterface $defaultStockProvider,
        GetReservationsQuantityInterface $getReservationsQuantity,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        StockIndexTableNameResolverInterface $stockIndexTableNameResolver
    ) {
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->resource = $resource;
        $this->helper = $helper;
    }

    public function execute(string $sku, string $regionId, string $stockId): ?float
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();
        if ($this->defaultStockProvider->getId() == $stockId) {
            return null;
        }
        if (isset(Region::REGION_INDEXER_QUANTITY_MAPPING[$regionId])) {
            $col = Region::REGION_INDEXER_QUANTITY_MAPPING[$regionId];
        } else {
            $col = IndexStructure::QUANTITY;
        }
        try {
            $stockItemTableName = $this->stockIndexTableNameResolver->execute($stockId);
            $select->from($stockItemTableName, $col)->where(IndexStructure::SKU . ' = ?', $sku);
            $regionQty = $connection->fetchOne($select) ?: 0;

            $stockItemConfig = $this->getStockItemConfiguration->execute($sku, $stockId);
            $minQty = $stockItemConfig->getMinQty();

            return $regionQty
                + $this->getReservationsQuantity->execute($sku, $stockId)
                - $minQty;
        } catch (\Exception $e) {
            $this->helper->debug($e);
        }

        return null;
    }
}
