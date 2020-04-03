<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Order\Processor;
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
    private $quoteRepository;

    /**
     * @var AddressFactory
     */
    private $customerAddressFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Processor
     */
    private $orderProcessor;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param AddressFactory $customerAddressFactory
     * @param LoggerInterface $logger
     * @param Processor $orderProcessor
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        AddressFactory $customerAddressFactory,
        LoggerInterface $logger,
        Processor $orderProcessor
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->orderProcessor = $orderProcessor;
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
        $order->setDistrict($quote->getDistrict());
        $order->setShippingServiceId($quote->getShippingServiceId());

        try {
            $order->save();
            $this->orderProcessor->syncOrder($order);
        } catch (Exception $e) {
            $this->logger->error(__(
                'Can\'t send order with increment ID %1 to giaohangnhanh.',
                $order->getIncrementId()
            ));
        }

        $customerAddressId = $quote->getShippingAddress()->getCustomerAddressId();
        $address = $this->customerAddressFactory->create()->load($customerAddressId);

        if ($address->getId()) {
            try {
                $address->setData('district', $quote->getDistrict());
                $address->save();
            } catch (Exception $e) {
                $this->logger->error(__(
                    'Can\'t set district for customer address with ID %1.',
                    $customerAddressId
                ));
            }
        }
    }
}
