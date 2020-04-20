<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address as MageQuoteAddress;

class Address
{
    public function afterExportCustomerAddress(MageQuoteAddress $subject, $result)
    {
        if ($district = $subject->getDistrict()) {
            $result->getExtensionAttributes()->setDistrict($district);
        }

        return $result;
    }
}
