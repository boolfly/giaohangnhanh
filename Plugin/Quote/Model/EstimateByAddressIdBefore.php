<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model;

use Magento\Customer\Model\AddressFactory;
use Magento\Quote\Model\ShippingMethodManagement as MageShippingMethodManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class EstimateByAddressIdBefore
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Quote\Model
 */
class EstimateByAddressIdBefore
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
     * @param MageShippingMethodManagement $subject
     * @param $cartId
     * @param $addressId
     * @throws NoSuchEntityException
     */
    public function beforeEstimateByAddressId(
        MageShippingMethodManagement $subject,
        $cartId,
        $addressId
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $address = $this->customerAddressFactory->create()->load($addressId);

        if ($address->getId()) {
            $district = $address->getDistrict();
            $quote->getShippingAddress()->setDistrict($district);
        }
    }
}
