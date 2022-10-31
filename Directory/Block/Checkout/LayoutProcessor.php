<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magenest\Directory\Helper\Data;

/**
 * Class LayoutProcessor
 * @package Magenest\Directory\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * Constructor.
     *
     * @param Data $dataHelper
     */
    public function __construct(
        Data $dataHelper
    )
    {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $jsLayout['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCityOptions();
        $jsLayout['components']['checkoutProvider']['dictionaries']['district_id'] = $this->getDistrictOptions();
        $jsLayout['components']['checkoutProvider']['dictionaries']['ward_id'] = $this->getWardOptions();
        if (isset($jsLayout['components']['checkoutProvider']['dictionaries']['country_id'])) {
            $countryList = $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'];
            $vnCountry = $this->getCountryOption($countryList);
            $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'] = $vnCountry;
        }

        $paymentLayout = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list'];

        foreach (array_keys($paymentLayout['children']) as $elementName) {
            if (strpos($elementName, '-form') !== false) {
                $paymentCode = str_replace('-form', '', $elementName);
                $formFields = array_replace_recursive(
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                    ['children']['payments-list']['children'][$elementName]['children']['form-fields']['children'],
                    [
                        'city' => [
                            'visible' => false,
                        ],
                        'country_id' => [
                            'visible' => false,
                        ],
                        'city_id' => [
                            'component' => 'Magenest_Directory/js/form/element/city',
                            'label' => __('City'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.city',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                'field' => 'country_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.city_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.city_id'
                            ]
                        ],
                        'district' => [
                            'visible' => false,
                        ],
                        'district_id' => [
                            'component' => 'Magenest_Directory/js/form/element/district',
                            'label' => __('District'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.district',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.city_id',
                                'field' => 'city_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.district_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.district_id'
                            ]
                        ],
                        'ward' => [
                            'visible' => false,
                        ],
                        'ward_id' => [
                            'component' => 'Magenest_Directory/js/form/element/ward',
                            'label' => __('Ward'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.ward',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.district_id',
                                'field' => 'district_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.ward_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.ward_id'
                            ]
                        ],
                        'telephone' => [
                            'validation' => [
                                'required-entry' => true,
                                'mobileVN' => true
                            ],
                        ],
                        'postcode' => [
                            'visible' => false
                        ],
                        'region_id' => [
                            'visible' => false
                        ],
                        'street' => [
                            'sortOrder' => 100
                        ]
                    ]
                );

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['payments-list']['children'][$elementName]['children']['form-fields']['children'] = $formFields;
            }
        }

        return $jsLayout;
    }

    /**
     * @param $countryList
     * @param string $value
     * @return array
     */
    public function getCountryOption($countryList, $value='VN')
    {
        $result = [];
        foreach ($countryList as $country) {
            if (isset($country['value']) && $country['value'] == $value) {
                $result[] = $country;
            }
        }

        return $result;
    }

    /**
     * Get city options
     *
     * @return array
     */
    public function getCityOptions()
    {
        return $this->_dataHelper->getCityOptions();
    }

    /**
     * Get district options
     *
     * @return array
     */
    protected function getDistrictOptions()
    {
        return $this->_dataHelper->getDistrictOptions();
    }

    /**
     * Get ward options
     *
     * @return array
     */
    protected function getWardOptions()
    {
        return $this->_dataHelper->getWardOptions();
    }
}
