<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\Order\Processor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;

class SalesOrderAfterPlaceObserver implements ObserverInterface
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Processor
     */
    private $orderProcessor;

    /**
     * SalesOrderAfterPlaceObserver constructor.
     * @param Service $service
     * @param Processor $orderProcessor
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        Service $service,
        Processor $orderProcessor,
        QuoteRepository $quoteRepository
    ) {
        $this->service = $service;
        $this->orderProcessor = $orderProcessor;
        $this->quoteRepository = $quoteRepository;
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
        $this->orderProcessor->syncOrder($order);
    }
}
