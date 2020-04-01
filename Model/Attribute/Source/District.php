<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Attribute\Source;

use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;

class District extends AbstractSource
{
    /**
     * @var Information
     */
    private $storeInformation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * District constructor.
     * @param Config $config
     * @param Information $storeInformation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        Information $storeInformation,
        StoreManagerInterface $storeManager
    ) {
        $this->storeInformation = $storeInformation;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        $store = $this->storeManager->getStore();
        $storeInfo = $this->storeInformation->getStoreInformationObject($store);
        $districts = $this->config->getDistricts();
        $data = [];
        foreach ($districts as $district) {
            if ($district['region_id'] == $storeInfo->getRegionId()) {
                $data[] = [
                    'districtName' => $district['district_name'],
                    'districtID' => $district['district_id']
                ];
            }
        }

        if (!$this->_options) {
            $this->_options[] = ['label' => __('Please select a district.'), 'value' => ''];

            if ($data) {
                foreach ($data as $districtItem) {
                    $this->_options[] = ['label' => $districtItem['districtName'], 'value' => $districtItem['districtID']];
                }
            }
        }
        return $this->_options;
    }
}
