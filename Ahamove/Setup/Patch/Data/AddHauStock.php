<?php

namespace Magenest\Ahamove\Setup\Patch\Data;

use Magenest\Directory\Model\CityFactory;
use Magenest\Ahamove\Model\RegionModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Inventory\Model\ResourceModel\Source as SourceResource;
use Magento\Inventory\Model\SourceFactory;
use Magento\Inventory\Model\StockFactory;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterfaceFactory;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryApi\Api\StockSourceLinksSaveInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddRichsStock
 *
 * @package Magenest\RegionStock\Setup\Patch\Data
 */
class AddHauStock implements DataPatchInterface
{
    const HAU_STOCK_NAME = 'Hau Stock';
    const PRIORITY_SOURCE  = 1;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var StockFactory
     */
    protected $stockFactory;
    /**
     * @var StockSourceLinksSaveInterface
     */
    protected $stockSourceLinksSave;
    /**
     * @var CityFactory
     */
    protected $cityFactory;
    /**
     * @var StockSourceLinkInterfaceFactory
     */
    protected $stockSourceLink;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var StockRepositoryInterface
     */
    protected $stockRepository;
    /**
     * @var SalesChannelInterfaceFactory
     */
    protected $salesChannelFactory;
    /**
     * @var SourceFactory
     */
    protected $sourceFactory;
    /**
     * @var SourceResource
     */
    protected $sourceResource;
    /**
     * @var string
     */
    protected $stockId;

    /**
     * AddRichsStock constructor.
     *
     * @param ModuleDataSetupInterface        $moduleDataSetup
     * @param StockFactory                    $stockFactory
     * @param StockSourceLinksSaveInterface   $stockSourceLinksSave
     * @param CityFactory                     $cityFactory
     * @param StockSourceLinkInterfaceFactory $stockSourceLink
     * @param DataObjectHelper                $dataObjectHelper
     * @param StoreManagerInterface           $storeManager
     * @param StockRepositoryInterface        $stockRepository
     * @param SalesChannelInterfaceFactory    $salesChannelFactory
     * @param SourceFactory                   $sourceFactory
     * @param SourceResource                  $sourceResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StockFactory $stockFactory,
        StockSourceLinksSaveInterface $stockSourceLinksSave,
        CityFactory $cityFactory,
        StockSourceLinkInterfaceFactory $stockSourceLink,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        StockRepositoryInterface $stockRepository,
        SalesChannelInterfaceFactory $salesChannelFactory,
        SourceFactory $sourceFactory,
        SourceResource $sourceResource
    ) {
        $this->moduleDataSetup      = $moduleDataSetup;
        $this->stockFactory         = $stockFactory;
        $this->stockSourceLinksSave = $stockSourceLinksSave;
        $this->cityFactory          = $cityFactory;
        $this->stockSourceLink      = $stockSourceLink;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->storeManager         = $storeManager;
        $this->stockRepository      = $stockRepository;
        $this->salesChannelFactory  = $salesChannelFactory;
        $this->sourceFactory        = $sourceFactory;
        $this->sourceResource       = $sourceResource;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->addStock();
        $this->createSource();
        $this->addSource();
        $this->moduleDataSetup->endSetup();
    }

    public function addStock()
    {
        $stock     = $this->stockFactory->create();
        $stockData = [
            StockInterface::NAME => self::HAU_STOCK_NAME
        ];
        $this->dataObjectHelper->populateWithArray($stock, $stockData, StockInterface::class);
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $this->populateWithWebsiteSalesChannels($stock, $websiteCode);
        $this->stockId = $this->stockRepository->save($stock);
    }

    public function createSource()
    {
        foreach (RegionModel::REGION_SOURCE_CITY as $sourceCode => $cityCode) {
            $city   = $this->cityFactory->create()->load($cityCode, 'code');
            $source = $this->sourceFactory->create();
            $source->setData(
                [
                    SourceInterface::SOURCE_CODE => $sourceCode,
                    SourceInterface::NAME        => $city->getName(),
                    SourceInterface::ENABLED     => 1,
                    SourceInterface::DESCRIPTION => $city->getName(),
                    SourceInterface::LATITUDE    => 0,
                    SourceInterface::LONGITUDE   => 0,
                    SourceInterface::COUNTRY_ID  => 'VN',
                    SourceInterface::POSTCODE    => '100000',
                    'city_id'                    => $city->getId()
                ]
            );
            $this->sourceResource->save($source);
        }
    }

    public function populateWithWebsiteSalesChannels($stock, $websiteCode)
    {
        $extensionAttributes   = $stock->getExtensionAttributes();
        $assignedSalesChannels = [$this->createSalesChannelByWebsiteCode($websiteCode)];
        $extensionAttributes->setSalesChannels($assignedSalesChannels);
    }

    public function createSalesChannelByWebsiteCode(string $websiteCode): SalesChannelInterface
    {
        $salesChannel = $this->salesChannelFactory->create();
        $salesChannel->setCode($websiteCode);
        $salesChannel->setType(SalesChannelInterface::TYPE_WEBSITE);

        return $salesChannel;
    }

    public function addSource()
    {
        $prepareSave = [];
        $sourceData  = [
            StockSourceLinkInterface::STOCK_ID => $this->stockId,
            StockSourceLinkInterface::PRIORITY => self::PRIORITY_SOURCE
        ];
        foreach (RegionModel::REGION_SOURCE_CITY as $sourceCode => $cityCode) {
            $source                                            = $this->stockSourceLink->create();
            $sourceData[StockSourceLinkInterface::SOURCE_CODE] = $sourceCode;
            $this->dataObjectHelper->populateWithArray($source, $sourceData, StockSourceLinkInterface::class);
            $prepareSave[] = $source;
        }
        if (!empty($prepareSave)) {
            $this->stockSourceLinksSave->execute($prepareSave);
        }
    }
}
