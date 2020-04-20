<?php

namespace Boolfly\GiaoHangNhanh\Api\Rest\Service\Order;

interface SynchronizerInterface
{
    const GHN_STATUS_FAIL = 0;
    const GHN_STATUS_SUCCESS = 1;

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $additionalData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Http_Client_Exception
     */
    public function syncOrder(\Magento\Sales\Model\Order $order, $additionalData);
}
