<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\ShippingInformationManagement as MageShippingInformationManagement;

/**
 * Class ShippingInformationManagement
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Checkout\Model
 */
class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var AddressFactory
     */
    private $customerAddressFactory;

    /**
     * ShippingInformationManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param AddressFactory $customerAddressFactory
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        AddressFactory $customerAddressFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerAddressFactory = $customerAddressFactory;
    }

    /**
     * @param MageShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @throws NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        MageShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        $extensionAttributes = $addressInformation->getExtensionAttributes();
        $shippingAddress = $quote->getShippingAddress();

        if ($shippingAddress->getDistrict()) {
            return;
        }

        if (!$extensionAttributes->getDistrict()) {
            $customerAddressId = $addressInformation->getShippingAddress()->getCustomerAddressId();
            $address = $this->customerAddressFactory->create()->load($customerAddressId);
            $district = $address->getDistrict();
        } else {
            $district = $extensionAttributes->getDistrict();
        }

        $shippingAddress->setDistrict($district);
    }
}
