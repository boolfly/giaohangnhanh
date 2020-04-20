<?php

namespace Boolfly\GiaoHangNhanh\Plugin\Customer\Model\ResourceModel;

use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\ResourceModel\AddressRepository as MageAddressRepository;

class AddressRepository
{
    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * AddressRepository constructor.
     * @param AddressRegistry $addressRegistry
     */
    public function __construct(
        AddressRegistry $addressRegistry
    ) {
        $this->addressRegistry = $addressRegistry;
    }

    public function afterGetById(MageAddressRepository $subject, $result, $addressId)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $result->getExtensionAttributes()->setDistrict($address->getDistrict());

        return $result;
    }
}
