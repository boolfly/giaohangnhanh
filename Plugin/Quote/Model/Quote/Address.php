<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model\Quote;

use Boolfly\GiaoHangNhanh\Setup\Patch\Data\AddressAttribute;
use Magento\Quote\Model\Quote\Address as MageQuoteAddress;

/**
 * Class Address
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Quote\Model\Quote
 */
class Address
{
    public function afterExportCustomerAddress(MageQuoteAddress $subject, $result)
    {
        if ($district = $subject->getDistrict()) {
            $result->setCustomAttribute(AddressAttribute::DISTRICT, $district);
        }

        return $result;
    }
}
