<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Shipping\Services;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Provider extends Service
{
    /**
     * @param array $request
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function getShippingServices($request)
    {
        $response = $this->makeRequest($this->config->getGettingServicesUrl(), $request);
        $shippingServices = [];

        if (is_array($this->responseReader->read($response))) {
            $shippingServices = $this->responseReader->read($response);
        }

        return $shippingServices;
    }
}
