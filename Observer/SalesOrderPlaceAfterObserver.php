<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\IntegrationBase\Model\Service\Command\CommandPoolInterface;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

/**
 * Class SalesOrderPlaceAfterObserver
 *
 * @package Boolfly\GiaoHangNhanh\Observer
 */
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
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * SalesOrderAfterSaveObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param CommandPoolInterface $commandPool
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        CommandPoolInterface $commandPool
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->commandPool = $commandPool;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (false !== strpos($order->getShippingMethod(), Config::GHN_CODE)) {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $shippingAddress = $quote->getShippingAddress();
            try {
                $this->commandPool->get('synchronize_order')->execute([
                    'order' => $order,
                    'district' => $shippingAddress->getDistrict(),
                    'shipping_service_id' => $shippingAddress->getShippingServiceId()
                ]);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                throw new Exception(__('This shipping method isn\'t valid now. Please select another shipping method.'));
            }
        }
    }
}
