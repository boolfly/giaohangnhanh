<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;

class SalesOrderAfterSaveObserver implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var AddressFactory
     */
    protected $customerAddressFactory;

    /**
     * SalesOrderAfterSaveObserver constructor.
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
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $quote = $this->quoteRepository->getActive($order->getQuoteId());
        $address = $this->customerAddressFactory->create()->load($quote->getShippingAddress()->getCustomerAddressId());
        $address->setData('district', $quote->getDistrict())->save();
    }
}
