<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const LBS_G = 453.59237;
    const KGS_G = 1000;
    const IS_ACTIVE = 'active';
    const TITLE = 'title';
    const NAME = 'name';
    const SALLOWSPECIFIC = 'sallowspecific';
    const API_TOKEN = 'carriers/giaohangnhanh_standard/api_token';
    const PAYMENT_TYPE = 'payment_type';
    const CALCULATING_FEE_URL = 'carriers/giaohangnhanh_standard/calculate_fee_url';
    const SYNCHRONIZING_ORDER_URL = 'carriers/giaohangnhanh_standard/sync_order_url';
    const GETTING_DISTRICTS_URL = 'carriers/giaohangnhanh_standard/get_districts_url';
    const NOTE_CODE = 'carriers/giaohangnhanh_standard/note_code';
    const DISTRICT = 'carriers/giaohangnhanh_standard/district';
    const GETTING_SERVICES_URL = 'carriers/giaohangnhanh_standard/get_services_url';

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getApiToken()
    {
        return $this->getConfig(self::API_TOKEN);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getCalculatingFeeUrl()
    {
        return $this->getConfig(self::CALCULATING_FEE_URL);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSynchronizingOrderUrl()
    {
        return $this->getConfig(self::SYNCHRONIZING_ORDER_URL);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGettingDistrictsUrl()
    {
        return $this->getConfig(self::GETTING_DISTRICTS_URL);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGettingServicesUrl()
    {
        return $this->getConfig(self::GETTING_SERVICES_URL);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getPaymentType()
    {
        return $this->getConfig(self::PAYMENT_TYPE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getNoteCode()
    {
        return $this->getConfig(self::NOTE_CODE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getStoreDistrict()
    {
        return $this->getConfig(self::DISTRICT);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getWeightUnit()
    {
        return $this->getConfig(Data::XML_PATH_WEIGHT_UNIT);
    }

    /**
     * @return array
     */
    public function getDistricts()
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = $connection->select()->from(
            ['districtTable' => $this->resourceConnection->getTableName('boolfly_giaohangnhanh_district')]
        )->joinLeft(
            ['regionTable' => $this->resourceConnection->getTableName('directory_country_region')],
            'regionTable.code = districtTable.province_id',
            'regionTable.region_id as region_id'
        );

        return $connection->fetchAll($sql);
    }

    /**
     * @param $path
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    private function getStoreId()
    {
        if (!$this->storeId) {
            $this->storeId = $this->storeManager->getStore()->getStoreId();
        }
        return $this->storeId;
    }
}
