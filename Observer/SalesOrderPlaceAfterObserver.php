<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Order\Synchronizer;
use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Zend_Http_Client_Exception;

class SalesOrderPlaceAfterObserver implements ObserverInterface
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
     * @var Synchronizer
     */
    private $synchronizer;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param Synchronizer $synchronizer
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        Synchronizer $synchronizer
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->synchronizer = $synchronizer;
    }

    /**
     * @inheritDoc
     * @param Observer $observer
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (false !== strpos($order->getShippingMethod(), Config::GHN_CODE)) {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $shippingAddress = $quote->getShippingAddress();
            $additionalData = [
                'district' => $shippingAddress->getDistrict(),
                'shipping_service_id' => $shippingAddress->getShippingServiceId()
            ];

            $this->synchronizer->syncOrder($order, $additionalData);
        }
    }
}
