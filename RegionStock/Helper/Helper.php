<?php /** @noinspection DuplicatedCode */
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SS extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SS
 */

namespace Magenest\RegionStock\Helper;

use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magenest\RegionPopup\Model\OptionSource\Region;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\RegionStock\Model\OptionSource\RegionSalable;

class Helper extends AbstractHelper
{
    const KEY_ENABLE_REGION_STOCK = 'cataloginventory/options/enable_region_stock';
    const KEY_DISPLAY_OUT_OF_STOCK_PRODUCT = 'cataloginventory/options/display_out_of_stock_product';

    protected $serializer;

    protected $_coreRegistry;

    protected $storeManager;

    protected $eavConfig;

    /**
     * Helper constructor.
     *
     * @param Attribute $eavConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavConfig,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Context $context
    ) {
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function registry($key)
    {
        return $this->_coreRegistry->registry($key);
    }

    public function register($key, $value)
    {
        try {
            $this->_coreRegistry->register($key, $value);
        } catch (\RuntimeException $e) {
            $this->debug($e);

            return false;
        }

        return true;
    }

    public function debug(\Exception $e)
    {
        $this->_logger->critical($e->getMessage());
    }

    public function unserialize($string)
    {
        if (!$this->isJson($string)) {

            return is_array($string) ?: [$string];
        }

        return $this->serializer->unserialize($string);
    }

    public function isJson($string)
    {
        if (!empty($string) && !is_array($string)) {
            json_decode($string);

            return (json_last_error() == JSON_ERROR_NONE);
        }

        return false;
    }

    public function serialize($string)
    {
        if ($this->isJson($string)) {
            return $string;
        }

        return $this->serializer->serialize($string);
    }

    public function getRegionSalableConfig($cookie)
    {
        return isset(RegionSalable::REGION_SALABLE_MAPPING[$cookie]) ? RegionSalable::REGION_SALABLE_MAPPING[$cookie] : null;
    }

    public function getStoreConfig($path, $scopeDefault = true, $scopeId = null)
    {
        if (!$scopeDefault || $scopeId) {
            try {
                $store = $this->storeManager->getStore();
                if ($store->getId() && $value = $this->scopeConfig->getValue($path, $store->getId(), ScopeInterface::SCOPE_STORE)) {
                    return $value;
                }
            } catch (NoSuchEntityException $e) {
                $this->debug($e);
            }
        }

        return $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function getCookie()
    {
//        $cookie = $this->locationCookie->getLocationCodeFromCookie();
//        if (!$cookie && !empty($this->registry('api_location'))) {
//            $cookie = $this->registry('api_location');
//        }
        $cookie = $this->registry('api_location');
        $regions = Region::getAllOptionsArray();

        return in_array($cookie, $regions) ? $cookie : null;
    }

    public function getAttributeId($code)
    {
        return $this->eavConfig->getIdByCode(Product::ENTITY, $code);
    }

    public function isEnableRegionStock()
    {
        return $this->getStoreConfig(self::KEY_ENABLE_REGION_STOCK);
    }

    public function isDisplayOutOfStockProduct()
    {
        return $this->getStoreConfig(self::KEY_DISPLAY_OUT_OF_STOCK_PRODUCT);
    }
}
