<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as MageLayoutProcessor;

class LayoutProcessor
{
    public function afterProcess(MageLayoutProcessor $subject, $result)
    {
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['district'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'district',
            ],
            'dataScope' => 'shippingAddress.custom_attributes.district',
            'label' => __('District'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => true],
            'sortOrder' => 255,
            'id' => 'district',
            'options' => [
                [
                    'value' => '',
                    'label' => __('Please select a district.'),
                ]
            ]
        ];

        return $result;
    }
}
