<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Exception;
use Psr\Http\Message\ResponseInterface;

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
     * @return array|ResponseInterface
     * @throws Exception
     */
    public function getAvailableServices($request)
    {
        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $request
        ];

        return $this->apiService->makeRequest($this->config->getGettingServicesUrl(), $data);
    }
}
