<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Exception;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param AddressFactory $customerAddressFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        AddressFactory $customerAddressFactory,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
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
        $address = $this->customerAddressFactory->create()
            ->load($quote->getShippingAddress()->getCustomerAddressId());

        try {
            $address->setData('district', $quote->getDistrict());
            $address->save();
        } catch (Exception $e) {
            $this->logger->error(__('Can\'t set district for customer address.'));
        }
    }
}
