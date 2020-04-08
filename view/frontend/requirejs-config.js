var config = {
    map: {
        "*": {
            'Magento_Checkout/js/model/shipping-save-processor/default': 'Boolfly_GiaoHangNhanh/js/model/shipping-save-processor/default',
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': 'Boolfly_GiaoHangNhanh/js/model/shipping-rate-processor/new-address'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/form/element/region': {
                'Boolfly_GiaoHangNhanh/js/form/element/region-mixin': true
            }
        }
    }
};