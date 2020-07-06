<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 *
 * @package Boolfly\GiaoHangNhanh\Model
 */
class Config
{
    const DEFAULT_PATH_PATTERN = 'giaohangnhanh_setting/%s/%s';
    const INTEGRATION_TYPE = 'general';
    const GHN_CODE = 'giaohangnhanh';
    const LBS_G = 453.59237;
    const KGS_G = 1000;
    const IS_ACTIVE = 'active';
    const TITLE = 'title';
    const NAME = 'name';
    const CALCULATING_FEE_URL = 'calculate_fee_url';
    const SYNCHRONIZING_ORDER_URL = 'sync_order_url';
    const GETTING_DISTRICTS_URL = 'get_districts_url';
    const GETTING_SERVICES_URL = 'get_services_url';
    const GETTING_ORDER_INFOR = 'get_order_infor_url';
    const CANCELING_ORDER_URL = 'cancel_order_url';

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
    public function getConfig($path)
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
