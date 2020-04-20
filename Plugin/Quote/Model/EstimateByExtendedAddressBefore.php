<?php

namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ShippingMethodManagement as MageShippingMethodManagement;

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

        if (!$district) {
            return;
        }

        $quote->getShippingAddress()->setDistrict($district);
    }
}
