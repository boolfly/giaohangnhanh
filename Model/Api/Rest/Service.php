<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest;

use Boolfly\GiaoHangNhanh\Model\Config;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Zend_Http_Client;
use Zend_Http_Client_Exception;

abstract class Service
{
    /**
     * @var LoggerInterface $log
     */
    protected $log;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Service constructor.
     * @param LoggerInterface $log
     * @param Config $config
     * @param SerializerInterface $serializer
     * @param ZendClientFactory $httpClientFactory
     */
    public function __construct(
        LoggerInterface $log,
        Config $config,
        SerializerInterface $serializer,
        ZendClientFactory $httpClientFactory
    ) {
        $this->log = $log;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @param $url
     * @param array $rawData
     * @param string $method
     * @return array
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    protected function makeRequest($url, $rawData = [], $method = Zend_Http_Client::POST)
    {
        $client = $this->httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['maxredirects' => 0, 'timeout' => 60]);
        $client->setRawData($this->serializer->serialize($rawData));
        $client->setMethod($method);

        try {
            $response = $client->request();
            $response = $this->processResponse($response);
            return $response;
        } catch (Exception $e) {
            throw new LocalizedException(__('The transaction details are unavailable. Please try again later.'));
        }
    }

    /**
     * @param $response
     * @return array
     */
    protected function processResponse($response)
    {
        $data = [];

        try {
            $body = $this->serializer->unserialize((string)$response->getBody());
        } catch (Exception $e) {
            $body = $e->getMessage();
        }

        $data['response_object'] = $body;
        $data['response_status_code'] = $response->getStatus();
        $data['response_status_message'] = $response->getMessage();

        return $data;
    }

    /**
     * Was the response successful?
     *
     * @param $response
     * @return bool
     */
    protected function checkResponse($response)
    {
        if (!empty($response['response_status_code'])) {
            $code = $response['response_status_code'];
            return (200 <= $code && 300 > $code);
        }

        return false;
    }
}
