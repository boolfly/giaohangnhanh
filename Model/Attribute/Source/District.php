<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class District extends AbstractSource
{
    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options[] = ['label' => __('Please select a district.'), 'value' => ''];
        }

        return $this->_options;
    }
}
