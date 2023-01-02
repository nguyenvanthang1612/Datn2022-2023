/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            storelocator: 'Magenest_StoreLocator/js/storelocator'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magenest_StoreLocator/js/order/place-order-mixin': true
            },
        }
    }
};
