<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\Plugin\Quote\Address;

/**
 * Class CustomAttributeList
 * @package Magenest\Directory\Model\Plugin\Quote\Address
 */
class CustomAttributeList
{
    protected $customerFormFactory;

    public function __construct(\Magento\Customer\Model\Metadata\FormFactory $customerFormFactory)
    {
        $this->customerFormFactory = $customerFormFactory;
    }

    /**
     * After get attributes
     *
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject
     * @param $result
     * @return array
     */
    public function afterGetAttributes(\Magento\Quote\Model\Quote\Address\CustomAttributeList $subject, $result)
    {
        $addressForm = $this->customerFormFactory->create('customer_address', 'adminhtml_customer_address');
        $attributes = $addressForm->getAttributes();
        return array_merge($result, [
            'city_id' => $attributes['city_id'] ?? true,
            'district' => $attributes['district'] ?? true,
            'district_id' => $attributes['district_id'] ?? true,
            'ward' => $attributes['ward'] ?? true,
            'ward_id' => $attributes['ward_id'] ?? true,
        ]);
    }
}
