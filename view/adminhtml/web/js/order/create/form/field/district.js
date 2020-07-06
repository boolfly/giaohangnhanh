/* global AdminOrder */
define([
    'jquery',
    'mage/translate',
    'domReady!'
], function ($, $t) {
    'use strict';

    $.widget('boolfly.adminDistrictUpdater', {
        options: {
            districtList: null,
            defaultDistrict: null,
            districtInput: '#order-shipping_address_district',
            districtSelector: '#order-shipping_address_district_id',
            regionSelector: '#order-shipping_address_region_id',
            addressBox: '#order-shipping_address_fields',
            districtSelectorID: 'order-shipping_address_district_id'
        },

        /**
         *
         * @private
         */
        _create: function () {
            this.prepairAdditionalField();
            this._bind();
        },

        _bind: function () {
            let self = this,
                districtInput = $(self.options.districtInput);

            $(self.options.regionSelector).on('change', function () {
                self.updateDistricts($(this).val());
                $(self.options.districtSelector).trigger("change");
            });

            $(self.options.districtSelector).on('change', function () {
                districtInput.val($(this).val());
                districtInput.trigger("change");
            });
        },

        prepairAdditionalField: function () {
            let self = this,
                defaultRegion = self.options.defaultRegion,
                defaultDistrict = self.options.defaultDistrict;

            $(self.options.districtInput).hide();

            $(self.options.addressBox + " > .field-district > .control").append(
                '<select id="' + self.options.districtSelectorID + '" type="text" class=" input-text admin__control-text" >' +
                '<option value="" selected>' + $t('Please select district.') + '</option>' +
                '</select>'
            );

            if (defaultRegion) {
                self.updateDistricts(defaultRegion);

                if (defaultDistrict) {
                    if ($(self.options.districtSelector + " option[value=" + defaultDistrict + "]").length > 0) {
                        $(self.options.districtSelector).val(defaultDistrict);
                    }
                }
            }
        },

        updateDistricts: function (regionId) {
            let self = this;
            let districtSelector = $(self.options.districtSelector);
            let districtList = self.options.jsonConfig.districts[parseInt(regionId)];

            districtSelector.children('option:not(:first)').remove();

            $.each(districtList, function (k, v) {
                districtSelector.append(new Option(v.districtName, v.districtID));
            });
        }
    });

    return $.boolfly.adminDistrictUpdater;
});

