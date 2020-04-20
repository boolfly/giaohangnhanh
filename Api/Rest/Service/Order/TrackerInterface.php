<?php

namespace Boolfly\GiaoHangNhanh\Api\Rest\Service\Order;

interface TrackerInterface
{
    const DEFAULT_ORDER_STATUS = 'ReadyToPick';

    /**
     * @param string $trackingCode
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function getOrderStatus($trackingCode);
}
