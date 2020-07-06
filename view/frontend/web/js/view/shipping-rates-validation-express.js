define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Boolfly_GiaoHangNhanh/js/model/shipping-rates-validator',
    'Boolfly_GiaoHangNhanh/js/model/shipping-rates-validation-rules'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    ghnShippingRatesValidator,
    ghnShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('giaohangnhanh_express', ghnShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('giaohangnhanh_express', ghnShippingRatesValidationRules);

    return Component;
});
