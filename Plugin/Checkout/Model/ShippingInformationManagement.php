<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement as MageShippingInformationManagement;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var AddressFactory
     */
    protected $customerAddressFactory;

    /**
     * ShippingInformationManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressFactory $customerAddressFactory
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        AddressRepositoryInterface $addressRepository,
        AddressFactory $customerAddressFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->addressRepository = $addressRepository;
        $this->customerAddressFactory = $customerAddressFactory;
    }

    /**
     * @param MageShippingInformationManagement $subject
     * @param $result
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterSaveAddressInformation(
        MageShippingInformationManagement $subject,
        $result,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $district = '';

        if ($extensionAttributes = $addressInformation->getExtensionAttributes()) {
            if ($extensionAttributes->getDistrict()) {
                $district = $extensionAttributes->getDistrict();
            }
        }

        if (!$district) {
            $customerAddressId = $addressInformation->getShippingAddress()->getCustomerAddressId();
            $customerAddress = $this->customerAddressFactory->create()->load($customerAddressId);
            if ($customerAddress->getId()) {
                $district = $customerAddress->getDistrict();
            }
        }

        $quote->setData('district', $district);
        $this->quoteRepository->save($quote);

        return $result;
    }
}
