<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ResourceConnection;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * ConfigProvider constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $districts = $this->getDistricts();
        $data = [];
        foreach ($districts as $district) {
            $data[$district['region_id']][] = [
                'districtName' => $district['district_name'],
                'districtID' => $district['district_id'],
                'provinceID' => $district['province_id']
            ];
        }
        return [
            'districts' => $data
        ];
    }

    /**
     * @return array
     */
    private function getDistricts()
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
}
