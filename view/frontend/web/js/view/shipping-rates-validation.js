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

    defaultShippingRatesValidator.registerValidator('giaohangnhanh_standard', ghnShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('giaohangnhanh_standard', ghnShippingRatesValidationRules);

    return Component;
});
