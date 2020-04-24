<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Shipping\Fee;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Calculator extends Service
{
    /**
     * @param array $request
     * @return float|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function calculate($request)
    {
        $response = $this->makeRequest(
            $this->config->getCalculatingFeeUrl(),
            $request
        );

        if ($this->checkResponse($response)) {
            return (float)$response['response_object']['data']['CalculatedFee'];
        }

        return null;
    }
}
