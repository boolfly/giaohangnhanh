<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Model;

use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\GiaoHangNhanh\Model\DistrictProvider;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\ShippingInformationManagement as MageShippingInformationManagement;

class ShippingInformationManagement
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
        if (false !== strpos($addressInformation->getShippingMethodCode(), Config::GHN_CODE)) {
            $quote = $this->quoteRepository->getActive($cartId);
            $extensionAttributes = $addressInformation->getExtensionAttributes();
            $shippingAddress = $quote->getShippingAddress();
            $district = '';

            if ($shippingAddress->getDistrict()) {
                return;
            }

            if (!$extensionAttributes->getDistrict()) {
                if ($customerAddressId = $shippingAddress->getCustomerAddressId()) {
                    $district = $this->districtProvider->getDistrictByCustomerAddressId($customerAddressId);
                }
            } else {
                $district = $extensionAttributes->getDistrict();
            }

            if ($district) {
                $shippingAddress->setDistrict($district);
            }
        }
    }
}
