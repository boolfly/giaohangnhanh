<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Order;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\Config;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend_Http_Client_Exception;

class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Information
     */
    private $storeInformation;

    /**
     * @var Service
     */
    private $apiService;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * Processor constructor.
     * @param Config $config
     * @param Service $apiService
     * @param StoreManagerInterface $storeManager
     * @param Information $storeInformation
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        Config $config,
        Service $apiService,
        StoreManagerInterface $storeManager,
        Information $storeInformation,
        AddressFactory $addressFactory
    ) {
        $this->config = $config;
        $this->apiService = $apiService;
        $this->storeManager = $storeManager;
        $this->storeInformation = $storeInformation;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @param Order $order
     * @param array $additionalData
     * @return array|ResponseInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function syncOrder(Order $order, $additionalData)
    {
        $config = $this->config;
        $weightRate = $config->getWeightUnit() == 'kgs' ? Config::KGS_G : Config::LBS_G;
        $store = $this->storeManager->getStore();
        $storeInfo = $this->storeInformation->getStoreInformationObject($store);
        $storeFormattedAddress = $this->storeInformation->getFormattedAddress($store);
        $storeDistrict = (int)$config->getStoreDistrict();

        $data = [
            'token' => $config->getApiToken(),
            'PaymentTypeID' => (int)$config->getPaymentType(),
            'FromDistrictID' => $storeDistrict,
            'ToDistrictID' => (int)$additionalData['district'],
            'ClientContactName' => $storeInfo->getName(),
            'ClientContactPhone' => $storeInfo->getPhone(),
            'ClientAddress' => $storeFormattedAddress,
            'CustomerName' => $order->getCustomerName(),
            'CustomerPhone' => $order->getShippingAddress()->getTelephone(),
            'ShippingAddress' => $order->getShippingAddress()->getStreetLine(1),
            'NoteCode' => $config->getNoteCode(),
            'ServiceID' => $additionalData['shipping_service_id'],
            'Weight' => $order->getWeight() * $weightRate,
            'Length' => 10,
            'Width' => 10,
            'Height' => 10,
            'CoDAmount' => 0,
            'ReturnContactName' => $storeInfo->getName(),
            'ReturnContactPhone' => $storeInfo->getPhone(),
            'ReturnAddress' => $storeFormattedAddress,
            'ReturnDistrictID' => $storeDistrict,
            'ExternalReturnCode' => $storeInfo->getName()
        ];

        return $this->apiService->makeRequest($config->getSynchronizingOrderUrl(), $data);
    }
}
