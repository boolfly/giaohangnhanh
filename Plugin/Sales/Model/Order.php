<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Sales\Model;

use Boolfly\GiaoHangNhanh\Model\Service\Helper\SubjectReader;
use Boolfly\IntegrationBase\Model\Service\Command\CommandPoolInterface;
use Exception;
use Magento\Sales\Model\Order as MageOrder;
use Psr\Log\LoggerInterface;

/**
 * Class Order
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Sales\Model
 */
class Order
{
    const DEFAULT_ORDER_STATUS = 'ReadyToPick';

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Order constructor.
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
     * @param MageOrder $subject
     * @param $result
     * @return bool
     */
    public function afterCanCancel(MageOrder $subject, $result)
    {
        if ($subject->getData('ghn_status') && $subject->getData('tracking_code')) {
            try {
                $commandResult = $this->commandPool->get('get_order_info')->execute(['order' => $subject]);
                $orderInfo = SubjectReader::readInfo($commandResult->get());
                if (SubjectReader::readCurrentOrderStatus($orderInfo) != self::DEFAULT_ORDER_STATUS) {
                    $result = false;
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $result;
    }
}
