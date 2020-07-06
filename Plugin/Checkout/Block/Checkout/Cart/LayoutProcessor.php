<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout\Cart;

use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\GiaoHangNhanh\Setup\Patch\Data\AddressAttribute;
use Magento\Checkout\Block\Cart\LayoutProcessor as MageLayoutProcessor;

/**
 * Class LayoutProcessor
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout\Cart
 */
class LayoutProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * LayoutProcessor constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function afterProcess(MageLayoutProcessor $subject, $jsLayout)
    {
        $jsLayout['components']['block-summary']['children']['block-shipping']
        ['children']['address-fieldsets']['children'][AddressAttribute::DISTRICT] = [
            'component' => 'Boolfly_GiaoHangNhanh/js/view/cart/shipping/district',
            'dataScope' => 'shippingAddress.district',
            'provider' => 'checkoutProvider',
            'sortOrder' => 152,
            'config' => [
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select'
            ],
            'visible' => true,
            'formElement' => 'select',
            'label' => __('District'),
            'options' => [],
            'value' => null,
            'filterBy' => [
                'target' => '${ $.provider }:${ $.parentScope }.region_id',
                'field' => 'region_id'
            ],
            'imports' => [
                'initialOptions' => 'index = checkoutProvider:dictionaries.district',
                'setOptions' => 'index = checkoutProvider:dictionaries.district'
            ]
        ];
        $jsLayout['components']['checkoutProvider']['dictionaries'][AddressAttribute::DISTRICT] = $this->config->getDistrictOptions();
        return $jsLayout;
    }
}
