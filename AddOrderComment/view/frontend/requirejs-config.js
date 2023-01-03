var config = {
config: {
    mixins: {
        'Magento_Checkout/js/action/place-order': {
            'Magenest_AddOrderComment/js/order/place-order-mixin': true
        },
        'Magento_Checkout/js/action/set-payment-information': {
            'Magenest_AddOrderComment/js/order/set-payment-information-mixin': true
        }
    }
}
};
