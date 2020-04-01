<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
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
     * Service constructor.
     * @param LoggerInterface $log
     */
    public function __construct(
        LoggerInterface $log
    ) {
        $this->log = $log;
        $this->client = new Client();
    }

    /**
     * @param $url
     * @param array $options
     * @param string $method
     * @return array|ResponseInterface
     * @throws Exception
     */
    public function makeRequest($url, $options = [], $method = self::POST)
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
