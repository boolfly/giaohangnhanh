<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Carrier;

use Boolfly\GiaoHangNhanh\Model\Api\Rest\Service;
use Boolfly\GiaoHangNhanh\Model\ServiceProvider;
use Exception;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
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

class GHN extends AbstractCarrier implements CarrierInterface
{
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
     * @var Information
     */
    private $storeInformation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Service
     */
    private $restService;

    /**
     * @var ShippingInformationInterface
     */
    private $addressInformation;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var AddressFactory
     */
    private $customerAddressFactory;

    /**
     * @var ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * GHN constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Information $storeInformation
     * @param StoreManagerInterface $storeManager
     * @param Service $restService
     * @param ShippingInformationInterface $addressInformation
     * @param Session $session
     * @param AddressFactory $customerAddressFactory
     * @param ServiceProvider $serviceProvider
     * @param Config $config
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Information $storeInformation,
        StoreManagerInterface $storeManager,
        Service $restService,
        ShippingInformationInterface $addressInformation,
        Session $session,
        AddressFactory $customerAddressFactory,
        ServiceProvider $serviceProvider,
        Config $config,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->storeInformation = $storeInformation;
        $this->storeManager = $storeManager;
        $this->restService = $restService;
        $this->addressInformation = $addressInformation;
        $this->session = $session;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->serviceProvider = $serviceProvider;
        $this->config = $config;
        $this->serializer = $serializer;
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
        $quote = $this->session->getQuote();
        $rate = $this->config->getWeightUnit() == 'kgs' ? Config::KGS_G : Config::LBS_G;
        $request = [
            'token' => 'TokenStaging',
            'Weight' => $request->getPackageWeight() * $rate,
            'FromDistrictID' => (int)$this->config->getStoreDistrict(),
            'ToDistrictID' => (int)$this->getDistrictId($quote)
        ];
        $serviceId = $this->getAvailableServices($request);
        $request['ServiceID'] = $serviceId;
        $quote->setData('shipping_service_id', $serviceId);

        try {
            $quote->save();
        } catch (Exception $e) {
            $this->_logger->error(__('Can\'t save quote.'));
        }

        $response = $this->restService->makeRequest(
            $this->config->getCalculatingFeeUrl(),
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $request
            ]
        )['response_object'];

        return !empty($response->data->CalculatedFee) ? $response->data->CalculatedFee : null;
    }

    /**
     * @param $request
     * @return string
     * @throws Exception
     */
    private function getAvailableServices($request)
    {
        $response = $this->serviceProvider->getAvailableServices($request)['response_object']->data;

        if (is_array($response)) {
            foreach ($response as $serviceItem) {
                if (!empty($serviceItem->Name)) {
                    $serviceType = $this->_code == 'giaohangnhanh_express' ? 'Nhanh' : 'Chuẩn';

                    if ($serviceItem->Name == $serviceType) {
                        return $serviceItem->ServiceID;
                    }
                }
            }
        }

        return '';
    }

    /**
     * @param Quote $quote
     * @return mixed|string
     * @throws Exception
     */
    private function getDistrictId(Quote $quote)
    {
        $data = $this->serializer->unserialize(file_get_contents('php://input'));
        $districtId = '';

        //After selecting shipping method and go to payment step
        if (!empty($quote->getDistrict())) {
            $districtId = $quote->getDistrict();
        } else {
            //If the shipping address is new
            if (!empty($data['address'])) {
                $customAttributes = $data['address']['custom_attributes'];

                if (null !== $customAttributes) {
                    foreach ($customAttributes as $attribute) {
                        if ($attribute['attribute_code'] == 'district') {
                            $districtId = $attribute['value'];
                            break;
                        }
                    }
                }
            }

            //If existing shipping address has been selected
            if (!empty($data['addressId'])) {
                $customerAddress = $this->customerAddressFactory->create()->load($data['addressId']);

                if ($customerAddress->getId()) {
                    $districtId = $customerAddress->getDistrict();
                }
            }

            //Set districtId to quote, this value will be used in payment step
            if (!$quote->getDistrict()) {
                $quote->setDistrict($districtId);
            }
        }

        return $districtId;
    }
}
