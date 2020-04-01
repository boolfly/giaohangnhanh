<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest;

use Boolfly\GiaoHangNhanh\Model\Config;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Json\Json;

class Service
{
    /**
     * post
     */
    const POST = 'post';

    /**
     * get
     */
    const GET = 'get';

    /**
     * put
     */
    const PUT = 'put';

    /**
     * patch
     */
    const PATCH = 'patch';

    /**
     * delete
     */
    const DELETE = 'delete';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Information
     */
    private $storeInformation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Service constructor.
     * @param LoggerInterface $log
     * @param Config $config
     * @param Information $storeInformation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $log,
        Config $config,
        Information $storeInformation,
        StoreManagerInterface $storeManager
    ) {
        $this->log = $log;
        $this->config = $config;
        $this->storeInformation = $storeInformation;
        $this->storeManager = $storeManager;
        $this->client = new Client();
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

        return $this->makeRequest($this->config->getGettingDistrictsUrl(), $data);
    }

    /**
     * @param $request
     * @return array|ResponseInterface
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function estimateShippingCost($request)
    {
        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $request
        ];

        return $this->makeRequest($this->config->getCalculatingFeeUrl(), $data);
    }

    public function getAvailableServices($request)
    {
        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $request
        ];

        return $this->makeRequest('https://dev-online-gateway.ghn.vn/apiv3-api/api/v1/apiv3/FindAvailableServices', $data);
    }

    /**
     * @param Order $order
     * @return array|ResponseInterface
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function syncOrder(Order $order)
    {
        $config = $this->config;
        $weightRate = $config->getWeightUnit() == 'kgs' ? Config::KGS_G : Config::LBS_G;
        $store = $this->storeManager->getStore();
        $storeInfo = $this->storeInformation->getStoreInformationObject($store);
        $storeFormattedAddress = $this->storeInformation->getFormattedAddress($store);
        $storeDistrict = (int)$config->getStoreDistrict();
        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'token' => $config->getApiToken(),
                'PaymentTypeID' => $config->getPaymentType(),
                'FromDistrictID' => $storeDistrict,
                'ToDistrictID' => (int)$order->getDistrict(),
                'ClientContactName' => $storeInfo->getName(),
                'ClientContactPhone' => $storeInfo->getPhone(),
                'ClientAddress' => $storeFormattedAddress,
                'CustomerName' => $order->getCustomerName(),
                'CustomerPhone' => $order->getShippingAddress()->getTelephone(),
                'ShippingAddress' => $order->getShippingAddress()->getStreetLine(1),
                'NoteCode' => $config->getNoteCode(),
                'ServiceID' => $order->getShippingServiceId(),
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
            ]
        ];

        return $this->makeRequest($config->getSynchronizingOrderUrl(), $data);
    }

    /**
     * @param $url
     * @param array $options
     * @param string $method
     * @return array|ResponseInterface
     * @throws Exception
     */
    private function makeRequest($url, $options = [], $method = self::POST)
    {
        $response = [
            'is_successful' => false
        ];
        try {
            /** @var ResponseInterface $response */
            $response = $this->client->$method($url, $options);
            $response = $this->processResponse($response);
            $response['is_successful'] = true;
        } catch (BadResponseException $e) {
            $this->log->error('Bad Response: ' . $e->getMessage());
            $this->log->error((string)$e->getRequest()->getBody());
            $response['response_status_code'] = $e->getCode();
            $response['response_status_message'] = $e->getMessage();
            $response = $this->processResponse($response);
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $this->log->error($errorResponse->getStatusCode() . ' ' . $errorResponse->getReasonPhrase());
                try {
                    $body = $this->processResponse($errorResponse);
                } catch (Exception $e) {
                    $this->log->error('Exception: ' . $e->getMessage());
                    $response['exception_code'] = $e->getCode();
                }
                $response = array_merge($response, $body);
            }
            $response['exception_code'] = $e->getCode();
        } catch (Exception $e) {
            $this->log->error('Exception: ' . $e->getMessage());
            $response['exception_code'] = $e->getCode();
        }

        return $response;
    }

    /**
     * Process the response and return an array
     *
     * @param ResponseInterface|array $response
     * @return array
     * @throws Exception
     */
    private function processResponse($response)
    {
        if (is_array($response)) {
            return $response;
        }

        try {
            $body = Json::decode((string)$response->getBody());
        } catch (Exception $e) {
            $body = $e->getMessage();
        }

        $data['response_object'] = $body;
        $data['response_status_code'] = $response->getStatusCode();
        $data['response_status_message'] = $response->getReasonPhrase();

        return $data;
    }

    /**
     * Was the response successful?
     *
     * @param $response
     * @return bool
     */
    public function checkResponse($response)
    {
        if (!empty($response['response_status_code'])) {
            $code = $response['response_status_code'];
            return (200 <= $code && 300 > $code);
        }

        return false;
    }
}
