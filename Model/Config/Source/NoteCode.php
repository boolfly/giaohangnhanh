<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class NoteCode implements ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'CHOTHUHANG', 'label' => __('Allow trying item')],
            ['value' => 'CHOXEMHANGKHONGTHU', 'label' => __('Allow checking item, but not trying')],
            ['value' => 'KHONGCHOXEMHANG', 'label' => __('Don\'t allow checking item')]
        ];
    }
}
