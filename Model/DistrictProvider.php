<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Http\Message\ResponseInterface;
use Zend_Http_Client_Exception;

class DistrictProvider
{
    /**
     * @var Service
     */
    private $apiService;

    /**
     * @var Config
     */
    private $config;

    /**
     * DistrictProvider constructor.
     * @param Service $apiService
     * @param Config $config
     */
    public function __construct(
        Service $apiService,
        Config $config
    ) {
        $this->apiService = $apiService;
        $this->config = $config;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function getDistrictList()
    {
        $data = [];
        $response = $this->apiService->makeRequest(
            $this->config->getGettingDistrictsUrl(),
            ['token' => $this->config->getApiToken()]
        );

        if ($this->apiService->checkResponse($response)) {
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
