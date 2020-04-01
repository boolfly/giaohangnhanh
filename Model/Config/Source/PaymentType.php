<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

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
