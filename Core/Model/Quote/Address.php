<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Rich's extension
 * NOTICE OF LICENSE
 *
 * @author SamNguyen
 * @category Magenest
 * @package Magenest_Rich's
 */

namespace Magenest\Core\Model\Quote;

class Address extends \Magento\Quote\Model\Quote\Address
{
    const VIETNAMESE_DIRECTORY_FIELDS = [
        'city_id',
        'district',
        'district_id',
        'ward',
        'ward_id'
    ];

    /**
     * Verify custom attributes set on $data and unset if not a valid custom attribute
     *
     * @param array $data
     * @return array processed data
     */
    protected function filterCustomAttributes($data)
    {
        if (empty($data[self::CUSTOM_ATTRIBUTES])) {
            return $data;
        }
        if (isset($data[self::CUSTOM_ATTRIBUTES][0])) {
            $data[self::CUSTOM_ATTRIBUTES] = $this->flattenCustomAttributesArrayToMap($data[self::CUSTOM_ATTRIBUTES]);
        }
        $customAttributesCodes         = $this->getCustomAttributesCodes();
        $data[self::CUSTOM_ATTRIBUTES] = array_intersect_key(
            (array) $data[self::CUSTOM_ATTRIBUTES],
            array_flip($customAttributesCodes)
        );
        foreach ($data[self::CUSTOM_ATTRIBUTES] as $code => $value) {
            if (is_array($value) && isset($value['attribute_code']) && isset($value['value'])) {
                $data[self::CUSTOM_ATTRIBUTES][$code] = $this->customAttributeFactory->create()
                    ->setAttributeCode($value['attribute_code'])
                    ->setValue($value['value']);
            } elseif (!($value instanceof \Magento\Framework\Api\AttributeInterface)) {
                $data[self::CUSTOM_ATTRIBUTES][$code] = $this->customAttributeFactory->create()
                    ->setAttributeCode($code)
                    ->setValue($value);
            }
        }
        return $data;
    }

    /**
     * Convert the custom attributes array format to map format
     *
     * The method \Magento\Framework\Reflection\DataObjectProcessor::buildOutputDataArray generates a custom_attributes
     * array representation where each custom attribute is a sub-array with a `attribute_code and value key.
     * This method maps such an array to the plain code => value map format exprected by filterCustomAttributes
     *
     * @param array[] $customAttributesData
     * @return array
     */
    private function flattenCustomAttributesArrayToMap(array $customAttributesData): array
    {
        return array_reduce(
            $customAttributesData,
            function (array $acc, array $customAttribute): array {
                $acc[$customAttribute['attribute_code']] = $customAttribute['value'];
                return $acc;
            },
            []
        );
    }
}