<?php

namespace Boolfly\GiaoHangNhanh\Plugin\Sales\Block\Adminhtml\Order\Create\Billing;

use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address as MageAddress;

class Address
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * Address constructor.
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param MageAddress $subject
     * @throws LocalizedException
     */
    public function beforeGetFormValues(MageAddress $subject)
    {
        $customerAddressId = $subject->getAddressId();

        if ($customerAddressId) {
            try {
                $customerAddress = $this->addressRepository->getById($customerAddressId);
                $district = $customerAddress->getCustomAttribute('district');

                if ($district) {
                    $subject->getCreateOrderModel()->getBillingAddress()->setDistrict($district->getValue());
                }
            } catch (LocalizedException $e) {
                throw new LocalizedException(
                    __("No such customer address with ID %1.", $customerAddressId)
                );
            }
        }
    }
}
