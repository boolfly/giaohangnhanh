<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Exception;
use Magento\Customer\Model\AddressFactory;
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
     * @var AddressFactory
     */
    private $customerAddressFactory;

    /**
     * DistrictProvider constructor.
     * @param Service $apiService
     * @param Config $config
     * @param AddressFactory $customerAddressFactory
     */
    public function __construct(
        Service $apiService,
        Config $config,
        AddressFactory $customerAddressFactory
    ) {
        $this->apiService = $apiService;
        $this->config = $config;
        $this->customerAddressFactory = $customerAddressFactory;
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

    /**
     * @param int $customerAddressId
     * @return string
     */
    public function getDistrictByCustomerAddressId($customerAddressId)
    {
        $address = $this->customerAddressFactory->create()->load($customerAddressId);

        if (!$address->getId()) {
            return '';
        }

        return $address->getDistrict() ?: '';
    }
}
