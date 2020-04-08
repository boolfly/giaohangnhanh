define([], function () {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            return {
                'country_id': {
                    'required': true
                },
                'region_id': {
                    'required': true
                },
                'district': {
                    'required': true
                }
            };
        }
    };
});
