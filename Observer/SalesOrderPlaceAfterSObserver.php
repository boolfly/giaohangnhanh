<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Api\Rest\Service\Order\SynchronizerInterface;
use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class SalesOrderPlaceAfterSObserver implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SynchronizerInterface
     */
    private $synchronizer;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param SynchronizerInterface $synchronizer
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        SynchronizerInterface $synchronizer
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->synchronizer = $synchronizer;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (false !== strpos($order->getShippingMethod(), Config::GHN_CODE)) {
            $quote = $this->quoteRepository->getActive($order->getQuoteId());
            $shippingAddress = $quote->getShippingAddress();
            $additionalData = [
                'district' => $shippingAddress->getDistrict(),
                'shipping_service_id' => $shippingAddress->getShippingServiceId()
            ];

            $this->synchronizer->syncOrder($order, $additionalData);
        }
    }
}
