define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            imports: {
                update: 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id:value'
            }
        },

        update: function (value) {
            if (value === 'VN') {
                this.validation['required-entry'] = true;
                this.required(true);
                this.setVisible(true);
            } else {
                this.setVisible(false);
            }
        }
    });
});