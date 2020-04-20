<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Sales\Model;

use Boolfly\GiaoHangNhanh\Api\Rest\Service\Order\TrackerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order as MageOrder;
use Zend_Http_Client_Exception;

class Order
{
    /**
     * @var TrackerInterface
     */
    private $tracker;

    /**
     * Order constructor.
     * @param TrackerInterface $tracker
     */
    public function __construct(TrackerInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param MageOrder $subject
     * @param $result
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function afterCanCancel(MageOrder $subject, $result)
    {
        $trackingCode = $subject->getData('tracking_code');
        $status = $subject->getData('ghn_status');

        if ($status && $trackingCode) {
            if ($this->tracker->getOrderStatus($trackingCode) != TrackerInterface::DEFAULT_ORDER_STATUS) {
                $result = false;
            }
        }

        return $result;
    }
}
