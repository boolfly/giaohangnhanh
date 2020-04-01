<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Http\Message\ResponseInterface;

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
     * @return array|ResponseInterface
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getDistrictList()
    {
        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => ['token' => $this->config->getApiToken()]
        ];

        return $this->apiService->makeRequest($this->config->getGettingDistrictsUrl(), $data);
    }
}
