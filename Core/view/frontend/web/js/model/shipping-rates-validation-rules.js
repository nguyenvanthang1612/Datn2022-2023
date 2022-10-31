/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [],
    function () {
        'use strict';
        return {
            getRules: function() {
                return {
                    'city_id': {
                        'required': true
                    },
                    'district_id': {
                        'required': true
                    },
                    'ward_id': {
                        'required': true
                    }
                };
            }
        };
    }
);
