<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Order;

use Boolfly\GiaoHangNhanh\Api\Rest\Service\Order\TrackerInterface;
use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Tracker extends Service implements TrackerInterface
{
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
        $data = '';

        if ($this->checkResponse($response)) {
            $data = $response['response_object']['data']['CurrentStatus'];
        }

        return $data;
    }
}
