<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Block\Cart;

use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Checkout\Block\Cart\LayoutProcessor as MageLayoutProcessor;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use Magento\Directory\Model\TopDestinationCountries;
use Magento\Framework\App\ObjectManager;

class LayoutProcessor extends MageLayoutProcessor
{
    /**
     * @var TopDestinationCountries
     */
    private $topDestinationCountries;

    /**
     * @var Config
     */
    private $config;

    /**
     * LayoutProcessor constructor.
     * @param AttributeMerger $merger
     * @param CountryCollection $countryCollection
     * @param RegionCollection $regionCollection
     * @param Config $config
     * @param TopDestinationCountries|null $topDestinationCountries
     */
    public function __construct(
        AttributeMerger $merger,
        CountryCollection $countryCollection,
        RegionCollection $regionCollection,
        Config $config,
        TopDestinationCountries $topDestinationCountries = null
    ) {
        parent::__construct($merger, $countryCollection, $regionCollection, $topDestinationCountries);
        $this->config = $config;
        $this->topDestinationCountries = $topDestinationCountries ?:
            ObjectManager::getInstance()->get(TopDestinationCountries::class);
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process($jsLayout)
    {
        $elements = [
            'city' => [
                'visible' => $this->isCityActive(),
                'formElement' => 'input',
                'label' => __('City'),
                'value' =>  null
            ],
            'country_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('Country'),
                'options' => [],
                'value' => null
            ],
            'region_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('State/Province'),
                'options' => [],
                'value' => null
            ],
            'postcode' => [
                'visible' => true,
                'formElement' => 'input',
                'label' => __('Zip/Postal Code'),
                'value' => null
            ],
            'district' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('District'),
                'options' => [],
                'value' => null
            ]
        ];

        if (!isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $jsLayout['components']['checkoutProvider']['dictionaries'] = [
                'country_id' => $this->countryCollection->loadByStore()->setForegroundCountries(
                    $this->topDestinationCountries->getTopDestinations()
                )->toOptionArray(),
                'region_id' => $this->regionCollection->addAllowedCountriesFilter()->toOptionArray(),
                'district' => $this->config->getDistrictOptions()
            ];
        }
        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']['children']
            ['address-fieldsets']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children'];
            $fieldSetPointer = $this->merger->merge($elements, 'checkoutProvider', 'shippingAddress', $fieldSetPointer);
            $fieldSetPointer['region_id']['config']['skipValidation'] = true;
        }
        return $jsLayout;
    }
}
