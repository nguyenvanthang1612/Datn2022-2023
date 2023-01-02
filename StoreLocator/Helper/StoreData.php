<?php

namespace Magenest\StoreLocator\Helper;

use Magento\Framework\App\ResourceConnection;

class StoreData
{
    /**
     * @var \Magenest\StoreLocator\Model\StoreFactory
     */
    protected $storeFactory;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    protected $storeResourceCollection;

    /**
     * StoreData constructor.
     * @param \Magenest\StoreLocator\Model\StoreFactory $storeFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magenest\StoreLocator\Model\ResourceModel\Store\CollectionFactory $storeFactory,
        ResourceConnection $resourceConnection,
        \Magenest\StoreLocator\Model\ResourceModel\Store\Collection $storeResourceCollection
    ) {
        $this->storeFactory = $storeFactory;
        $this->resourceConnection = $resourceConnection;
        $this->storeResourceCollection = $storeResourceCollection;
    }

    /**
     * @return mixed
     */
    public function getStoreData()
    {
        return $this->storeFactory->create()->load()->getItems();
//        $connection = $this->resourceConnection->getConnection();
//        $tableName = $connection->getTableName('magenest_store_locator');
//        $select = $connection->select()
//            ->from($tableName, ['id', 'name']);
//        return $connection->fetchAll($select);

    }
}
