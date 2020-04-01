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
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Store\Model\Information;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Directory\Helper\Data;
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
     * @var string
     */
    private $weightUnit;

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
        $this->weightUnit = $scopeConfig->getValue(
            Data::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE
        );
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
     */
    private function estimateShippingCost(RateRequest $request)
    {
        $rate = $this->weightUnit == 'kgs' ? Config::KGS_G : Config::LBS_G;
        $data = json_decode(file_get_contents('php://input'), true);
        $quote = $this->session->getQuote();
        $districtId = '';

        if (!empty($data['address'])) {
            $customAttributes = $data['address']['custom_attributes'];
        } elseif (!empty($data['addressInformation'])) {
            $customAttributes = $data['addressInformation']['shipping_address']['customAttributes'];
        } else {
            $customAttributes = null;
        }

        if (null !== $customAttributes) {
            foreach ($customAttributes as $attribute) {
                if ($attribute['attribute_code'] == 'district') {
                    $districtId = $attribute['value'];
                    break;
                }
            }
        }

        if (!$districtId) {
            $customerAddress = $this->customerAddressFactory->create();

            if (!empty($data['addressId'])) {
                $districtId = $customerAddress->load($data['addressId'])->getDistrict();
            } elseif ($customerAddressId = $quote->getShippingAddress()->getCustomerAddressId()) {
                $districtId = $customerAddress->load($customerAddressId)->getDistrict();
            } else {
                $districtId = $quote->getDistrict();
            }
        }

        $request = [
            'token' => 'TokenStaging',
            'Weight' => $request->getPackageWeight() * $rate,
            'FromDistrictID' => 1443,
            'ToDistrictID' => (int)$districtId
        ];
        $serviceId = $this->getAvailableServices($request);
        $request['ServiceID'] = $serviceId;
        $this->session->getQuote()->setData('shipping_service_id', $serviceId);

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
}
