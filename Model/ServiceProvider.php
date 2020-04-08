<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Http\Message\ResponseInterface;
use Zend_Http_Client_Exception;

class ServiceProvider
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
     * ServiceProvider constructor.
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
     * @param $request
     * @return array
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     * @throws LocalizedException
     */
    public function getAvailableServices($request)
    {
        $response = $this->apiService->makeRequest($this->config->getGettingServicesUrl(), $request);
        $availableServices = [];

        if ($this->apiService->checkResponse($response)) {
            $data = $response['response_object']['data'];

            if (is_array($data)) {
                $availableServices = $data;
            }
        }

        return $availableServices;
    }
}
