<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Shipping\Services;

use Boolfly\GiaoHangNhanh\Api\Rest\Service\Shipping\Services\ProviderInterface;
use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class Provider extends Service implements ProviderInterface
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
        $data = [];

        if ($this->checkResponse($response)) {

            if (is_array($response['response_object']['data'])) {
                $data = $response['response_object']['data'];
            }
        }

        return $data;
    }
}
