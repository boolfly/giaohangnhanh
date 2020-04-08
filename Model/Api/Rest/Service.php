<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Zend_Http_Client;
use Zend_Http_Client_Exception;
use Zend_Http_Response;

class Service
{
    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Service constructor.
     * @param LoggerInterface $log
     * @param SerializerInterface $serializer
     * @param ZendClientFactory $httpClientFactory
     */
    public function __construct(
        LoggerInterface $log,
        SerializerInterface $serializer,
        ZendClientFactory $httpClientFactory
    ) {
        $this->log = $log;
        $this->serializer = $serializer;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @param $url
     * @param array $rawData
     * @param string $method
     * @return mixed|Zend_Http_Response
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function makeRequest($url, $rawData = [], $method = Zend_Http_Client::POST)
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
     * @param Zend_Http_Response $response
     * @return mixed
     */
    private function processResponse($response)
    {
        if (is_array($response)) {
            return $response;
        }

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
    public function checkResponse($response)
    {
        if (!empty($response['response_status_code'])) {
            $code = $response['response_status_code'];
            return (200 <= $code && 300 > $code);
        }

        return false;
    }
}
