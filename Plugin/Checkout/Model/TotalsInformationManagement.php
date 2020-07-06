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

use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Model\TotalsInformationManagement as MageTotalsInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class TotalsInformationManagement
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Checkout\Model
 */
class TotalsInformationManagement
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * TotalsInformationManagement constructor.
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function beforeCalculate(
        MageTotalsInformationManagement $subject,
        $cartId,
        TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $shippingAddress = $quote->getShippingAddress();

        if ($shippingAddress->getDistrict()) {
            return;
        }

        $district = $addressInformation->getExtensionAttributes()->getDistrict();
        $shippingAddress->setDistrict($district);
    }
}
