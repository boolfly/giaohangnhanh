define([
    'jquery',
    'Boolfly_GiaoHangNhanh/js/view/checkout/shipping/district'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        }
    });
});