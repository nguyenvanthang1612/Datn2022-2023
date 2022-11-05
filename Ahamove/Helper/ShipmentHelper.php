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

namespace Magenest\Ahamove\Helper;

use Magenest\Ahamove\Model\RegionModel;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ShipmentHelper
 *
 * @package Magenest\Ahamove\Helper
 */
class ShipmentHelper extends \Magenest\Core\Helper\Helper
{
    const NAME_SERVICE_PATH = 'carriers/ahamove/name_service';
    const MAX_COD_SERVICE_PATH = 'carriers/ahamove/max_cod_service';
    const SANDBOX_URL    = 'https://apistg.ahamove.com/';
    const PRODUCTION_URL = 'https://api.ahamove.com/';
    const DISTANCE_LIMIT = 15;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        Registry $registry,
        Context $context
    ) {
        parent::__construct($serializer, $storeManager, $registry, $context);
        $this->_logger = $logger;
    }

    /**
     * @return string
     */
    public function getUrlApi()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/sandbox') ? self::PRODUCTION_URL : self::SANDBOX_URL;
    }

    /**
     * @return string
     */
    public function getCarrierPriceType()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/price_type');
    }

    /**
     * @return string
     */
    public function getDistanceLimit()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/distance_limit') ?: self::DISTANCE_LIMIT;
    }

    /**
     * @return string
     */
    public function getCarrierTotalFee()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/total_fee');
    }

    /**
     * @return string
     */
    public function getCarrierEnableFreeshipping()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/enable_freeshipping');
    }

    /**
     * @return string
     */
    public function getCarrierApiToken()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/api_token');
    }

    /**
     * @return string
     */
    public function getCarrierApiKey()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/api_key');
    }

    /**
     * @return string
     */
    public function getCarrierFreeshippingSubTotal()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/freeshipping_subtotal');
    }

    /**
     * @return string
     */
    public function getCarrierAllowedMethods()
    {
        return $this->scopeConfig->getValue('carriers/ahamove/allowed_methods');
    }

    /**
     * @return array|null
     */
    public function getCarrierNameService()
    {
        return $this->scopeConfig->getValue(self::NAME_SERVICE_PATH) ? $this->unserialize($this->scopeConfig->getValue(self::NAME_SERVICE_PATH)) : null;
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function getMaxCodService()
    {
        return $this->scopeConfig->getValue(self::MAX_COD_SERVICE_PATH) ?
            $this->unserialize($this->scopeConfig->getValue(self::MAX_COD_SERVICE_PATH)) : null;
    }

    /**
     * @return array
     */
    public function getCarrierShippingMethod($sourceCode)
    {
        $code = $this->convertSourceCode($sourceCode);
        $allowsMethod = $this->getCarrierAllowedMethods();
        if ($allowsMethod) {
            $payload = [];
            $arr = explode(',', $allowsMethod);
            if (!empty($arr)) {
                foreach ($arr as $serviceId) {
                    if ($code && strpos($serviceId, $code) !== false) {
                        $payload[] = [
                            '_id' => $serviceId,
                        ];
                    }
                }

                return $payload;
            }
        }

        return [];
    }

    /**
     * @param $sourceCode
     *
     * @return string
     */
    public function convertSourceCode($sourceCode)
    {
        switch ($sourceCode) {
            case RegionModel::HANOI_SOURCE_CODE:
                return 'HAN';
            case RegionModel::DANANG_SOURCE_CODE:
                return 'DAD';
            case RegionModel::HCMC_SOURCE_CODE:
                return 'SGN';
            case RegionModel::CANTHO_SOURCE_CODE:
                return 'VCA';
        }
    }
}
