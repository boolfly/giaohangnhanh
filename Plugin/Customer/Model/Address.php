<?php

namespace Boolfly\GiaoHangNhanh\Plugin\Customer\Model;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address as MageCustomerAddress;

class Address
{
    public function afterUpdateData(MageCustomerAddress $subject, $result, AddressInterface $address)
    {
        $district = $address->getExtensionAttributes()->getDistrict();

        if ($district) {
            $result->setDistrict($district);
        }

        return $result;
    }
}
