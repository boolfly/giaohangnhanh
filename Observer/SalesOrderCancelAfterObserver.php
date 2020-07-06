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

use Boolfly\IntegrationBase\Model\Service\Command\CommandPoolInterface;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class SalesOrderCancelAfterObserver
 *
 * @package Boolfly\GiaoHangNhanh\Observer
 */
class SalesOrderCancelAfterObserver implements ObserverInterface
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SalesOrderCancelAfterObserver constructor.
     * @param CommandPoolInterface $commandPool
     * @param LoggerInterface $logger
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        LoggerInterface $logger
    ) {
        $this->commandPool = $commandPool;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getData('ghn_status') && $order->getData('tracking_code')) {
            try {
                $this->commandPool->get('cancel_order')->execute(['order' => $order]);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
