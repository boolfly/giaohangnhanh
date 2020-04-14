<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model;

use Magento\Customer\Model\AddressFactory;
use Magento\Quote\Model\ShippingMethodManagement as MageShippingMethodManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;

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
        $district = '';

        if ($address->getId()) {
            $district = $address->getDistrict();
        }

        if (!$district) {
            return;
        }

        $quote->getShippingAddress()->setDistrict($district);
    }
}
