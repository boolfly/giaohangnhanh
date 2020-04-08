<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\GiaoHangNhanh\Model\Order\Processor;
use Exception;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class SalesOrderPlaceAfterSObserver implements ObserverInterface
{
    const GHN_STATUS_FAIL = 0;
    const GHN_STATUS_SUCCESS = 1;

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
     * @var Service
     */
    private $apiService;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param AddressFactory $customerAddressFactory
     * @param LoggerInterface $logger
     * @param Processor $orderProcessor
     * @param Service $apiService
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        AddressFactory $customerAddressFactory,
        LoggerInterface $logger,
        Processor $orderProcessor,
        Service $apiService
    ) {
        $this->logger = $logger;
        $this->apiService = $apiService;
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

        if (false !== strpos($order->getShippingMethod(), Config::GHN_CODE)) {
            $quote = $this->quoteRepository->getActive($order->getQuoteId());
            $shippingAddress = $quote->getShippingAddress();
            $additionalData = [
                'district' => $shippingAddress->getDistrict(),
                'shipping_service_id' => $shippingAddress->getShippingServiceId()
            ];
            $response = $this->orderProcessor->syncOrder($order, $additionalData);

            if (true === $this->apiService->checkResponse($response)) {
                $order->setData('ghn_status', self::GHN_STATUS_SUCCESS);
            } else {
                $order->setData('ghn_status', self::GHN_STATUS_FAIL);
            }

            if ($customerAddressId = $shippingAddress->getCustomerAddressId()) {
                $customerAddress = $this->customerAddressFactory->create()->load($customerAddressId);

                if ($customerAddress->getId()) {
                    try {
                        $customerAddress->setData('district', $shippingAddress->getDistrict());
                        $customerAddress->save();
                    } catch (Exception $e) {
                        $this->logger->error(__(
                            'Can\'t set district for customer address with ID %1.',
                            $customerAddressId
                        ));
                    }
                }
            }
        }
    }
}
