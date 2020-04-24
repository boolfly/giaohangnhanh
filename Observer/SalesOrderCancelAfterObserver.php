<?php

namespace Boolfly\GiaoHangNhanh\Observer;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Order\Cancellation;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class SalesOrderCancelAfterObserver implements ObserverInterface
{
    const GHN_SUCCESS_CANCELING_STATUS = 1;

    /**
     * @var Cancellation
     */
    private $cancellation;

    /**
     * SalesOrderCancelAfterObserver constructor.
     * @param Cancellation $cancellation
     */
    public function __construct(Cancellation $cancellation)
    {
        $this->cancellation = $cancellation;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $status = $order->getData('ghn_status');
        $trackingCode = $order->getData('tracking_code');

        if ($status && $trackingCode) {
            $result = $this->cancellation->cancel($trackingCode);

            if ($result) {
                $order->setData('ghn_canceling_status', self::GHN_SUCCESS_CANCELING_STATUS);
            }
        }
    }
}
