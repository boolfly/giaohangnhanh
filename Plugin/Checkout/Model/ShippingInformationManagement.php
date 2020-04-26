<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Model;

use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Customer\Model\AddressFactory;
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
        $district = '';

        if ($shippingAddress->getDistrict()) {
            return;
        }

        if (!$extensionAttributes->getDistrict()) {
            $customerAddressId = $addressInformation->getShippingAddress()->getCustomerAddressId();

            if ($customerAddressId) {
                $address = $this->customerAddressFactory->create()->load($customerAddressId);

                if ($address->getId()) {
                    $district = $address->getDistrict();
                }
            }
        } else {
            $district = $extensionAttributes->getDistrict();
        }

        if ($district) {
            $shippingAddress->setDistrict($district);
        }
    }
}
