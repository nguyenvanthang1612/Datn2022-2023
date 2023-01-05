define([
    'jquery',
    'mage/utils/wrapper',
    'Magenest_AddOrderComment/js/order/order-comments-assigner'
], function ($, wrapper, orderCommentsAssigner) {
    'use strict';

    return function (placeOrderAction) {

        /** Override place-order-mixin for set-payment-information action as they differs only by method signature */
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            orderCommentsAssigner(paymentData);

            return originalAction(messageContainer, paymentData);
        });
    };
});
