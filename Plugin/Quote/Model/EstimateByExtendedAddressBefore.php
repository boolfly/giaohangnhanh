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
        $checkoutFields = $address->getExtensionAttributes()->getCheckoutFields();
        $district = '';

        foreach ($checkoutFields as $field) {
            if ($field->getAttributeCode() == 'district') {
                $district = $field->getValue();
                break;
            }
        }

        if (!$district) {
            return;
        }

        $quote->getShippingAddress()->setDistrict($district);
    }
}
