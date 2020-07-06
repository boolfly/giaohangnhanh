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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ShippingMethodManagement as MageShippingMethodManagement;

/**
 * Class EstimateByExtendedAddressBefore
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Quote\Model
 */
class EstimateByExtendedAddressBefore
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * EstimateByExtendedAddressBefore constructor.
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }


    /**
     * @param MageShippingMethodManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     * @throws NoSuchEntityException
     */
    public function beforeEstimateByExtendedAddress(
        MageShippingMethodManagement $subject,
        $cartId,
        AddressInterface $address
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $district = $address->getExtensionAttributes()->getDistrict();
        $quote->getShippingAddress()->setDistrict($district);
    }
}
