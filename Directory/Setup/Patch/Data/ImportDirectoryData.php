<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kootoro extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Kootoro
 */

namespace Magenest\Directory\Setup\Patch\Data;

use Magenest\Directory\Model\ResourceModel\City as CityResourceModel;
use Magenest\Directory\Model\ResourceModel\District as DistrictResourceModel;
use Magenest\Directory\Model\ResourceModel\Ward as WardResourceModel;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class ImportDirectoryData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var CityResourceModel
     */
    private $cityResourceModel;

    /**
     * @var DistrictResourceModel
     */
    private $districtResourceModel;

    /**
     * @var WardResourceModel
     */
    private $wardResourceModel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ComponentRegistrar $componentRegistrar
     * @param ReadFactory $readFactory
     * @param JsonSerializer $jsonSerializer
     * @param CityResourceModel $cityResourceModel
     * @param DistrictResourceModel $districtResourceModel
     * @param WardResourceModel $wardResourceModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ComponentRegistrar $componentRegistrar,
        ReadFactory $readFactory,
        JsonSerializer $jsonSerializer,
        CityResourceModel $cityResourceModel,
        DistrictResourceModel $districtResourceModel,
        WardResourceModel $wardResourceModel,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->cityResourceModel = $cityResourceModel;
        $this->districtResourceModel = $districtResourceModel;
        $this->wardResourceModel = $wardResourceModel;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->importDirectoryData();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Import Directory data
     *
     * @return void
     */
    private function importDirectoryData()
    {
        try {
            $moduleDir = $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                'Magenest_Directory'
            );
            $directoryRead = $this->readFactory->create($moduleDir);

            //Import city
            $fileAbsolutePath = $moduleDir . '/Setup/city.json';
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
            $cities = $this->cityResourceModel->createMultiple(
                $this->jsonSerializer->unserialize($directoryRead->readFile($filePath))
            );

            //Import district
            $fileAbsolutePath = $moduleDir . '/Setup/district.json';
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
            $districts = $this->districtResourceModel->createMultiple(
                $this->jsonSerializer->unserialize($directoryRead->readFile($filePath)),
                $cities
            );

            //Import ward
            $fileAbsolutePath = $moduleDir . '/Setup/ward.json';
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
            $this->wardResourceModel->createMultiple(
                $this->jsonSerializer->unserialize($directoryRead->readFile($filePath)),
                $districts
            );
        } catch (ValidatorException|LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
