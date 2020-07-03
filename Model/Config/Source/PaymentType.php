<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PaymentType
 *
 * @package Boolfly\GiaoHangNhanh\Model\Config\Source
 */
class PaymentType implements ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Shop/Seller')], ['value' => 2, 'label' => __('Buyer/Consignee')]];
    }
}
