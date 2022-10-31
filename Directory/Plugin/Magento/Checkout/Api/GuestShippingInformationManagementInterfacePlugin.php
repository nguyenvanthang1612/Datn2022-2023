<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Directory extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Directory
 */

namespace Magenest\Directory\Plugin\Magento\Checkout\Api;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\GuestShippingInformationManagementInterface;

class GuestShippingInformationManagementInterfacePlugin
{
    /**
     * @param GuestShippingInformationManagementInterface $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return array
     */
    public function beforeSaveAddressInformation(
        GuestShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $customAttr = [
            'city_id',
            'district',
            'district_id',
            'ward',
            'ward_id'
        ];
        $addressShipping = $addressInformation->getShippingAddress();
        foreach ($customAttr as $attr) {
            if (isset($addressShipping->getCustomAttribute($attr)->getValue()['value'])) {
                $attrValue = $addressShipping->getCustomAttribute($attr)->getValue()['value'];
                $addressShipping->setCustomAttribute($attr, $attrValue);
            }
        }
        return [
            $cartId,
            $addressInformation
        ];
    }
}
