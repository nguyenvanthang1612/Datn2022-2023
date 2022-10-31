<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\Address\Validator;

use Laminas\Validator\ValidatorChain;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\Escaper;
use Magento\Store\Model\ScopeInterface;

class Country implements ValidatorInterface
{
    /**
     * @var Data
     */
    protected $_directoryData;

    /**
     * @var AllowedCountries
     */
    protected $_allowedCountriesReader;

    /**
     * @var CollectionFactory
     */
    protected $_cityCollection;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * @param Data $directoryData
     * @param AllowedCountries $allowedCountriesReader
     * @param CollectionFactory $cityCollectionFactory
     * @param Escaper $escaper
     * @param ValidatorChain $validatorChain
     */
    public function __construct(
        Data $directoryData,
        AllowedCountries $allowedCountriesReader,
        CollectionFactory $cityCollectionFactory,
        Escaper $escaper,
        ValidatorChain $validatorChain
    ) {
        $this->_cityCollection = $cityCollectionFactory;
        $this->_directoryData = $directoryData;
        $this->_allowedCountriesReader = $allowedCountriesReader;
        $this->escaper = $escaper;
        $this->validatorChain = $validatorChain;
    }

    /**
     * @inheritdoc
     */
    public function validate(AbstractAddress $address)
    {
        return $this->validateCountry($address);
    }

    /**
     * Validate country existence.
     *
     * @param AbstractAddress $address
     *
     * @return array
     */
    private function validateCountry(AbstractAddress $address)
    {
        $countryId = $address->getCountryId();
        $errors = [];

        if (!$this->getNotEmptyValidate($countryId)) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'countryId']);
        } elseif (!in_array($countryId, $this->getWebsiteAllowedCountries($address), true)) {
            //Checking if such country exists.
            $errors[] = __(
                'Invalid value of "%value" provided for the %fieldName field.',
                [
                    'fieldName' => 'countryId',
                    'value'     => $this->escaper->escapeHtml($countryId)
                ]
            );
        }

        return $errors;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function getNotEmptyValidate($value)
    {
        $validator = $this->validatorChain;
        $validator->attachByName('NotEmpty');
        return $validator->isValid($value);
    }

    /**
     * Return allowed counties per website.
     *
     * @param AbstractAddress $address
     *
     * @return array
     */
    private function getWebsiteAllowedCountries(AbstractAddress $address): array
    {
        $storeId = $address->getData('store_id');

        return $this->_allowedCountriesReader->getAllowedCountries(ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Validate region existence.
     *
     * @param AbstractAddress $address
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function validateRegion(AbstractAddress $address)
    {
        $countryId = $address->getCountryId();
        $regionCollection = $this->_cityCollection->create();
        $region = $address->getRegion();
        $regionId = (string)$address->getRegionId();
        $allowedRegions = $regionCollection->getAllIds();
        $isRegionRequired = $this->_directoryData->isRegionRequired($countryId);
        $errors = [];
        if ($isRegionRequired && empty($allowedRegions) && !$this->getNotEmptyValidate($region)) {
            //If region is required for country and country doesn't provide regions list
            //region must be provided.
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'region']);
        } elseif ($allowedRegions && !$this->getNotEmptyValidate($regionId) && $isRegionRequired) {
            //If country actually has regions and requires you to
            //select one then it must be selected.
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'regionId']);
        } elseif ($allowedRegions && $regionId && !in_array($regionId, $allowedRegions, true)) {
            //If a region is selected then checking if it exists.
            $errors[] = __(
                'Invalid value of "%value" provided for the %fieldName field.',
                [
                    'fieldName' => 'regionId',
                    'value'     => $this->escaper->escapeHtml($regionId)
                ]
            );
        }
        return $errors;
    }
}
