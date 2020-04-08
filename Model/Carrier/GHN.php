<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Carrier;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\Carrier\GHN\Express;
use Boolfly\GiaoHangNhanh\Model\Carrier\GHN\Standard;
use Boolfly\GiaoHangNhanh\Model\ServiceProvider;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
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
     * @var Service
     */
    protected $restService;

    /**
     * @var ServiceProvider
     */
    protected $serviceProvider;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

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
     * @param Service $restService
     * @param ServiceProvider $serviceProvider
     * @param Config $config
     * @param QuoteRepository $quoteRepository
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Service $restService,
        ServiceProvider $serviceProvider,
        Config $config,
        QuoteRepository $quoteRepository,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->restService = $restService;
        $this->serviceProvider = $serviceProvider;
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
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
    private function estimateShippingCost(RateRequest $request)
    {
        $quote = $this->quoteRepository->getActive(
            $request->getAllItems()[0]->getQuoteId()
        );
        $shippingAddress = $quote->getShippingAddress();
        $districtId = $shippingAddress->getDistrict();

        if ($districtId) {
            $rate = $this->config->getWeightUnit() == 'kgs' ? Config::KGS_G : Config::LBS_G;
            $requestBody = [
                'token' => $this->config->getApiToken(),
                'Weight' => $request->getPackageWeight() * $rate,
                'FromDistrictID' => (int)$this->config->getStoreDistrict(),
                'ToDistrictID' => (int)$districtId
            ];

            $this->availableServices = $this->serviceProvider->getAvailableServices($requestBody);

            if ($serviceId = $this->getAvailableService()) {
                $requestBody['ServiceID'] = $serviceId;

                if ($request->getLimitCarrier()) {
                    $shippingAddress->setData('shipping_service_id', $serviceId);

                    try {
                        $shippingAddress->save();
                    } catch (Exception $e) {
                        $this->_logger->error(__('Can\'t save shipping address.'));
                    }
                }

                $response = $this->restService->makeRequest(
                    $this->config->getCalculatingFeeUrl(),
                    [
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ],
                        'json' => $requestBody
                    ]
                );

                if ($this->restService->checkResponse($response)) {
                    return $response['response_object']->data->CalculatedFee;
                }
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getAvailableService()
    {
        if (count($this->availableServices)) {
            foreach ($this->availableServices as $serviceItem) {
                if (!empty($serviceItem->Name)) {

                    if ($serviceItem->Name == static::SERVICE_NAME) {
                        return $serviceItem->ServiceID;
                    }
                }
            }
        }

        return '';
    }
}
