<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Order;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Cancellation extends Service
{
    /**
     * @param string $trackingCode
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function cancel($trackingCode)
    {
        $response = $this->makeRequest(
            $this->config->getCancelingOrderUrl(),
            [
                'token' => $this->config->getApiToken(),
                'OrderCode' => $trackingCode
            ]
        );

        return $response ? true : false;
    }
}
