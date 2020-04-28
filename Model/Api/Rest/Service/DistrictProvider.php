<?php declare(strict_types=1);

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
        $districts = [];
        $response = $this->makeRequest(
            $this->config->getGettingDistrictsUrl(),
            ['token' => $this->config->getApiToken()]
        );

        if (is_array($this->responseReader->read($response))) {
            $districts = $this->responseReader->read($response);
        }

        return $districts;
    }
}
