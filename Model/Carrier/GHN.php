<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Carrier;

use Boolfly\GiaoHangNhanh\Helper\Rate;
use Boolfly\GiaoHangNhanh\Model\Service\Helper\SubjectReader;
use Boolfly\GiaoHangNhanh\Model\Service\Request\AbstractDataBuilder;
use Boolfly\IntegrationBase\Model\Service\Command\CommandException;
use Boolfly\IntegrationBase\Model\Service\Command\CommandPoolInterface;
use Exception;
use Magento\Framework\Exception\NotFoundException;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Psr\Log\LoggerInterface;
use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;

/**
 * Class GHN
 *
 * @package Boolfly\GiaoHangNhanh\Model\Carrier
 */
abstract class GHN extends AbstractCarrier implements CarrierInterface
{
    const SERVICE_NAME = 'GHN';

    /**
     * @var string
     */
    protected $_code = 'giaohangnhanh';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Rate
     */
    private $helperRate;

    /**
     * @var array
     */
    protected $availableServices = [];

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * GHN constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Config $config
     * @param Rate $helperRate
     * @param CommandPoolInterface $commandPool
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Config $config,
        Rate $helperRate,
        CommandPoolInterface $commandPool,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->config = $config;
        $this->helperRate = $helperRate;
        $this->commandPool = $commandPool;
    }

    /**
     * @inheritDoc
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag(Config::IS_ACTIVE)) {
            return false;
        }

        if ($shippingCost = $this->estimateShippingCost($request)) {
            /** @var Result $result */
            $result = $this->rateResultFactory->create();
            /** @var Method $method */
            $method = $this->rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData(Config::TITLE));
            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData(Config::NAME));
            $method->setPrice($shippingCost);
            $method->setCost($shippingCost);

            $result->append($method);

            return $result;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData(Config::NAME)];
    }


    /**
     * @param RateRequest $request
     * @return string|null
     */
    protected function estimateShippingCost(RateRequest $request)
    {
        $shippingAddress = $request->getShippingAddress();
        $shippingFee = 0;

        try {
            $this->prepareServices($request);
            if ($serviceId = $this->getAvailableService()) {
                $shippingAddress->setData('shipping_service_id', $serviceId);
                $commandResult = $this->commandPool->get('calculate_rate')->execute(
                    [
                        'rate_request' => $request,
                        'service_id' => $serviceId
                    ]
                );
                $rate = SubjectReader::readRate($commandResult->get());
                $shippingFee = SubjectReader::readCalculatedFee($rate);
            }
            return $this->helperRate->getAmountByStoreCurrency($shippingFee);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param RateRequest $request
     * @throws CommandException
     * @throws NotFoundException
     */
    protected function prepareServices($request)
    {
        $this->availableServices = SubjectReader::readServices(
            $this->commandPool->get('get_services')->execute(['rate_request' => $request])->get()
        );
    }

    /**
     * @return mixed|null
     */
    protected function getAvailableService()
    {
        if (count($this->availableServices)) {
            foreach ($this->availableServices as $serviceItem) {
                if (is_array($serviceItem) && SubjectReader::readServiceName($serviceItem) == static::SERVICE_NAME) {
                    return $serviceItem[AbstractDataBuilder::SERVICE_ID];
                }
            }
        }

        return null;
    }
}
