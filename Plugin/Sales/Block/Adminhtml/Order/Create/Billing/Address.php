<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Sales\Block\Adminhtml\Order\Create\Billing;

use Boolfly\GiaoHangNhanh\Setup\Patch\Data\AddressAttribute;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address as MageAddress;

/**
 * Class Address
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Sales\Block\Adminhtml\Order\Create\Billing
 */
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
                $district = $customerAddress->getCustomAttribute(AddressAttribute::DISTRICT);

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
