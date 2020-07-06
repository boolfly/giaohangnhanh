<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Service\Http\Client;

use Boolfly\IntegrationBase\Model\Logger\Logger;
use Boolfly\IntegrationBase\Model\Service\Http\ClientException;
use Boolfly\IntegrationBase\Model\Service\Http\ClientInterface;
use Boolfly\IntegrationBase\Model\Service\Http\ConverterException;
use Boolfly\IntegrationBase\Model\Service\Http\ConverterInterface;
use Boolfly\IntegrationBase\Model\Service\Http\TransferInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;

/**
 * Class Zend
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Http\Client
 */
class Zend implements ClientInterface
{
    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ZendClientFactory         $clientFactory
     * @param Logger                    $logger
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Logger $logger,
        ConverterInterface $converter = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->converter     = $converter;
        $this->logger        = $logger;
    }

    public function request(TransferInterface $transferObject)
    {
        $log    = [
            'request' => $this->converter ? $this->converter->convert($transferObject->getBody()) : $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];
        $result = [];
        /** @var ZendClient $client */
        $client = $this->clientFactory->create();
        $client->setConfig($transferObject->getClientConfig());
        $client->setMethod($transferObject->getMethod());
        $client->setRawData($transferObject->getBody());
        $client->setHeaders($transferObject->getHeaders());
        $client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());

        try {
            $response        = $client->request();
            $result          = $this->converter ? $this->converter->convert($response->getBody()) : [$response->getBody()];
            $log['response'] = $result;
        } catch (\Zend_Http_Client_Exception $e) {
            throw new ClientException(
                __($e->getMessage())
            );
        } catch (ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
