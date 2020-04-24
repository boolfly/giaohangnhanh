<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Http_Client_Exception;

class DistrictProvider extends Service
{
    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    public function getDistrictList()
    {
        $data = [];
        $response = $this->makeRequest(
            $this->config->getGettingDistrictsUrl(),
            ['token' => $this->config->getApiToken()]
        );

        if ($this->checkResponse($response)) {
            $responseObject = $response['response_object'];

            if (!empty($responseObject['data'])) {
                if (is_array($responseObject['data'])) {
                    $data = $responseObject['data'];
                }
            }
        }

        return $data;
    }
}
