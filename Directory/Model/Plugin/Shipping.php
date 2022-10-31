<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\Plugin;

use Magenest\Directory\Model\Plugin\Quote\Address;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Shipping
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param RateRequest $request
     *
     * @return RateRequest[]
     */
    public function beforeCollectRates(\Magento\Shipping\Model\Shipping $subject, RateRequest $request)
    {
        $customAttributes = $this->_registry->registry(Address::CUSTOM_ATTRIBUTE_KEY);
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                if ($attribute instanceof AttributeValue) {
                    $attribute = $attribute->__toArray();
                }

                if (in_array($attribute['attribute_code'], [
                    'city_id',
                    'district',
                    'district_id',
                    'ward',
                    'ward_id'
                ])) {
                    $request->setData('dest_' . $attribute['attribute_code'], $attribute['value']);
                }
            }

            $request->setData('custom_attributes', $customAttributes);
        }

        return [$request];
    }
}
