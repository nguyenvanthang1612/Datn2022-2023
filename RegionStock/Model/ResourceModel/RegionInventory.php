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

namespace Magenest\RegionStock\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;

class RegionInventory extends AbstractDb
{
    protected $_idFieldName = 'sku';

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        $connectionName = null
    ) {
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $stockId = $this->getStockIdForCurrentWebsite->execute();
        $mainTable = $this->stockIndexTableNameResolver->execute($stockId);
        $this->_init($mainTable, $this->_idFieldName);
    }
}
