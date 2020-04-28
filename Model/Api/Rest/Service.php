<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Helper\ResponseReaderInterface;
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
     * @var ResponseReaderInterface|null
     */
    protected $responseReader;

    /**
     * Service constructor.
     * @param LoggerInterface $log
     * @param Config $config
     * @param SerializerInterface $serializer
     * @param ZendClientFactory $httpClientFactory
     * @param ResponseReaderInterface|null $responseReader
     */
    public function __construct(
        LoggerInterface $log,
        Config $config,
        SerializerInterface $serializer,
        ZendClientFactory $httpClientFactory,
        ResponseReaderInterface $responseReader = null
    ) {
        $this->log = $log;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->httpClientFactory = $httpClientFactory;
        $this->responseReader = $responseReader;
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
            $responseData = [];

            if ($response->isSuccessful()) {
                $responseData = $this->serializer->unserialize((string)$response->getBody());
            } else {
                $this->log->error(__('Bad request.'));
            }

            return $responseData;
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
            throw new LocalizedException(__('The transaction details are unavailable. Please try again later.'));
        }
    }
}
