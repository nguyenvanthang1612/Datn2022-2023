/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Magenest_Core/js/model/shipping-rates-validator',
        'Magenest_Core/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        shippingRatesValidator,
        shippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('giaohangnhanh', shippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('giaohangnhanh', shippingRatesValidationRules);
        return Component;
    });
