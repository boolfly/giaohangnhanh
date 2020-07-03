define([
    'Magento_Customer/js/model/customer'
], function (customer) {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            let rules = {};
            if (!customer.isLoggedIn()) {
                rules = {
                    'district': {
                        'required': true
                    }
                };
            }

            return rules;
        }
    };
});
