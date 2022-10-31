<?php

namespace Magenest\Directory\Plugin\Magento\Checkout\Api;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class GuestPaymentInformationManagementInterfacePlugin
{
    /**
     * @param GuestPaymentInformationManagementInterface $subject
     * @param $cartId
     * @param $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $customAttr = [
                'city_id',
                'district',
                'district_id',
                'ward',
                'ward_id'
            ];
            foreach ($customAttr as $attr) {
                if (isset($billingAddress->getCustomAttribute($attr)->getValue()['value'])) {
                    $attrValue = $billingAddress->getCustomAttribute($attr)->getValue()['value'];
                    $billingAddress->setCustomAttribute($attr, $attrValue);
                }
            }
        }
    }
}
