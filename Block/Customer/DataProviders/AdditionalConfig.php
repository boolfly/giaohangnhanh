<?php

namespace Boolfly\GiaoHangNhanh\Block\Customer\DataProviders;

use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AdditionalConfig implements ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Config $config
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Config $config,
        SerializerInterface $serializer
    ) {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * Get districts
     *
     * @return array
     */
    private function getDistricts()
    {
        $districts = $this->config->getDistricts();
        $data = [];
        foreach ($districts as $district) {
            $data[$district['region_id']][] = [
                'districtName' => $district['district_name'],
                'districtID' => $district['district_id']
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getJsonData(): string
    {
        return $this->serializer->serialize([
            'districts' => $this->getDistricts()
        ]);
    }
}
