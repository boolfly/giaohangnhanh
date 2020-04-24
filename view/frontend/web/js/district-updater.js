define([
    'jquery',
    'underscore',
    'jquery/ui',
    'mage/validation',
    'domReady!'
], function ($, _) {
    'use strict';

    $.widget('boolfly.districtUpdater', {
        options: {
            isRegionRequired: true,
            districtList: null,
            defaultDistrict: ''
        },

        /**
         *
         * @private
         */
        _create: function () {
            let self = this;
            let defaultDistrict = self.options.defaultDistrict;
            let districtSelector = $(self.options.districtListId);
            let regionId = self.options.defaultRegion;

            self.updateDistricts(regionId);

            if (defaultDistrict) {
                districtSelector.val(defaultDistrict);
            }

            self._bind();
        },

        _bind: function () {
            let self = this;

            self.element.on('change', function () {
                self.updateDistricts($(this).val());
            });
        },

        updateDistricts: function (regionId) {
            let self = this;
            let districtSelector = $(self.options.districtListId);
            let districtList = self.options.jsonConfig.districts[regionId];

            districtSelector.children('option:not(:first)').remove();

            $.each(districtList, function (k, v) {
                districtSelector.append(new Option(v.districtName, v.districtID));
            });
        }
    });

    return $.boolfly.districtUpdater;
});
