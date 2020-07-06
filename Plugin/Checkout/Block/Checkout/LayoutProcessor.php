<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout;

use Boolfly\GiaoHangNhanh\Setup\Patch\Data\AddressAttribute;
use Magento\Checkout\Block\Checkout\LayoutProcessor as MageLayoutProcessor;

/**
 * Class LayoutProcessor
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout
 */
class LayoutProcessor
{
    public function afterProcess(MageLayoutProcessor $subject, $result)
    {
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children'][AddressAttribute::DISTRICT] = [
            'component' => 'Boolfly_GiaoHangNhanh/js/view/checkout/shipping/district',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => AddressAttribute::DISTRICT,
            ],
            'dataScope' => 'shippingAddress.custom_attributes.district',
            'label' => __('District'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => false],
            'sortOrder' => 255,
            'id' => AddressAttribute::DISTRICT,
            'imports' => [
                'initialOptions' => 'index = checkoutProvider:dictionaries.district',
                'setOptions' => 'index = checkoutProvider:dictionaries.district'
            ],
            'filterBy' => [
                'target' => 'checkoutProvider:shippingAddress.region_id',
                'field' => 'region_id'
            ],
            'deps' => 'checkoutProvider'
        ];

        return $result;
    }
}
