<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Quote\Model;

use Boolfly\GiaoHangNhanh\Model\DistrictProvider;
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
     * @var DistrictProvider
     */
    private $districtProvider;

    /**
     * ShippingInformationManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param DistrictProvider $districtProvider
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        DistrictProvider $districtProvider
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->districtProvider = $districtProvider;
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
        $district = $this->districtProvider->getDistrictByCustomerAddressId($addressId);

        if (!$district) {
            return;
        }

        $quote->getShippingAddress()->setDistrict($district);
    }
}
