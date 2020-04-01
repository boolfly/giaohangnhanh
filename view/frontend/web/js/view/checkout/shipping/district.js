define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function ($, Component, quote, rateRegistry) {
    'use strict';

    let checkoutConfigDistricts = window.checkoutConfig.districts;
    return Component.extend({
        defaults: {
            imports: {
                update: 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id:value'
            }
        },

        initialize: function () {
            this._super();
            let district = $('[name="custom_attributes[district]"]');
            district.on('change', function() {
                alert('kkk');
            });

        },

        update: function (value) {
            let district = $('[name="custom_attributes[district]"]');
            let districtList = checkoutConfigDistricts[parseInt(value)];
            district.children('option:not(:first)').remove();

            $.each(districtList, function (k, v) {
                district.append(new Option(v.districtName, v.districtID));
            });
        }
    });
});