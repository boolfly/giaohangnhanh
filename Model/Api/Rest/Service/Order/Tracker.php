<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Order;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Tracker extends Service
{
    const DEFAULT_ORDER_STATUS = 'ReadyToPick';

    /**
     * @param string $trackingCode
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function getOrderStatus($trackingCode)
    {
        $response = $this->makeRequest(
            $this->config->getGettingOrderInforUrl(),
            [
                'token' => $this->config->getApiToken(),
                'OrderCode' => $trackingCode
            ]
        );

        return $this->responseReader->read($response);
    }
}
