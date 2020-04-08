define(['jquery'], function ($) {
    'use strict';

    var mixin = {

        /**
         * @param {String} value
         */
        update: function (value) {
            this._super();
            let districtField = $('[name="custom_attributes[district]"]');

            if (value !== 'VN') {
                districtField.hide();
            } else {
                if (districtField.is(":hidden")) {
                    districtField.show();
                }
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});