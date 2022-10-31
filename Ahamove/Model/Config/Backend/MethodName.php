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

namespace Magenest\Ahamove\Model\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\Ahamove\Helper\ShipmentHelper;

/**
 * Class MethodName
 *
 * @package Magenest\Ahamove\Model\Config\Backend
 */
class MethodName extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;
    /**
     * @var \Magenest\Ahamove\Model\Config\Source\Api\Service
     */
    protected $serviceApi;
    /**
     * @var ShipmentHelper
     */
    protected $shipmentHelper;

    /**
     * MethodName constructor.
     *
     * @param ShipmentHelper $shipmentHelper
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magenest\Ahamove\Model\Config\Source\Api\Service $serviceApi
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ShipmentHelper $shipmentHelper,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magenest\Ahamove\Model\Config\Source\Api\Service $serviceApi,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->shipmentHelper = $shipmentHelper;
        $this->configWriter = $configWriter;
        $this->serviceApi = $serviceApi;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return MethodName
     */
    public function afterSave()
    {
        $services = $this->serviceApi->getCollectionSourceCode();
        if ($services) {
            $params = [];
            foreach ($services as $id => $service) {
                $params[$id] = [
                    'en' => $service['name_en_us'],
                    'vn' => $service['name_vi_vn']
                ];
            }
            $values = $this->shipmentHelper->serialize($params);
            $this->configWriter->save(
                ShipmentHelper::NAME_SERVICE_PATH,
                $values,
                $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $scopeId = 0
            );

            $this->saveMaxCod($services);
        }

        return parent::afterSave();
    }

    protected function saveMaxCod($services)
    {
        $params = [];
        foreach ($services as $id => $service) {
            $params[$id] = $service['max_cod'] ?? null;
        }
        $values = $this->shipmentHelper->serialize($params);
        $this->configWriter->save(
            ShipmentHelper::MAX_COD_SERVICE_PATH,
            $values,
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeId = 0
        );
    }
}
