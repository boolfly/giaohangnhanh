<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Carrier;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Shipping\Fee\Calculator;
use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service\Shipping\Services\Provider;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
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
use Zend_Http_Client_Exception;

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
     * @var Calculator
     */
    protected $calculator;

    /**
     * @var Provider
     */
    protected $serviceProvider;

    /**
     * @var array
     */
    protected $availableServices = [];

    /**
     * GHN constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Config $config
     * @param Calculator $calculator
     * @param Provider $serviceProvider
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Config $config,
        Calculator $calculator,
        Provider $serviceProvider,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->config = $config;
        $this->calculator = $calculator;
        $this->serviceProvider = $serviceProvider;
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
     * @return float|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    protected function estimateShippingCost(RateRequest $request)
    {
        $shippingAddress = $request->getShippingAddress();
        $districtId = $request->getDistrict();
        $shippingFee = null;

        if ($districtId) {
            $rate = $this->config->getWeightUnit() == 'kgs' ? Config::KGS_G : Config::LBS_G;
            $requestBody = [
                'token' => $this->config->getApiToken(),
                'Weight' => $request->getPackageWeight() * $rate,
                'FromDistrictID' => (int)$this->config->getStoreDistrict(),
                'ToDistrictID' => (int)$districtId
            ];

            $this->prepareServices($requestBody);

            if ($serviceId = $this->getAvailableService()) {
                $requestBody['ServiceID'] = $serviceId;
                $this->updateShippingServiceForAddress($request, $shippingAddress, $serviceId);
                $shippingFee = $this->calculator->calculate($requestBody);
            }
        }

        return $shippingFee;
    }

    /**
     * @param RateRequest $request
     * @param Address $shippingAddress
     * @param string $serviceId
     */
    protected function updateShippingServiceForAddress(RateRequest $request, Address $shippingAddress, $serviceId)
    {
        if ($request->getLimitCarrier()) {
            $shippingAddress->setData('shipping_service_id', $serviceId);
        }
    }

    /**
     * @param array $request
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Http_Client_Exception
     */
    protected function prepareServices($request)
    {
        $this->availableServices = $this->serviceProvider->getShippingServices($request);
    }

    /**
     * @return string
     */
    protected function getAvailableService()
    {
        if (count($this->availableServices)) {
            foreach ($this->availableServices as $serviceItem) {
                if (!empty($serviceItem['Name'])) {
                    if ($serviceItem['Name'] == static::SERVICE_NAME) {
                        return $serviceItem['ServiceID'];
                    }
                }
            }
        }

        return '';
    }
}
