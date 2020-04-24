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
    const GHN_CODE = 'giaohangnhanh';
    const LBS_G = 453.59237;
    const KGS_G = 1000;
    const IS_ACTIVE = 'active';
    const TITLE = 'title';
    const NAME = 'name';
    const SALLOWSPECIFIC = 'sallowspecific';
    const SPECIFICCOUNTRY = 'specificcountry';
    const API_TOKEN = 'giaohangnhanh_setting/general/api_token';
    const PAYMENT_TYPE = 'giaohangnhanh_setting/general/payment_type';
    const CALCULATING_FEE_URL = 'giaohangnhanh_setting/general/calculate_fee_url';
    const SYNCHRONIZING_ORDER_URL = 'giaohangnhanh_setting/general/sync_order_url';
    const GETTING_DISTRICTS_URL = 'giaohangnhanh_setting/general/get_districts_url';
    const NOTE_CODE = 'giaohangnhanh_setting/general/note_code';
    const DISTRICT = 'giaohangnhanh_setting/general/district';
    const GETTING_SERVICES_URL = 'giaohangnhanh_setting/general/get_services_url';
    const GETTING_ORDER_INFOR = 'giaohangnhanh_setting/general/get_order_infor_url';
    const CANCELING_ORDER_URL = 'giaohangnhanh_setting/general/cancel_order_url';

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
    public function getGettingOrderInforUrl()
    {
        return $this->getConfig(self::GETTING_ORDER_INFOR);
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
    public function getCancelingOrderUrl()
    {
        return $this->getConfig(self::CANCELING_ORDER_URL);
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
     * @return array
     */
    public function getDistrictOptions()
    {
        $districts = $this->getDistricts();
        $data = [];
        foreach ($districts as $district) {
            $districtName = $district['district_name'];
            $data[] = [
                'title' => $districtName,
                'value' => $district['district_id'],
                'region_id' => $district['region_id'],
                'label' => $districtName
            ];
        }
        return $data;
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
