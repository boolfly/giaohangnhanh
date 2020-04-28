<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout;

use Boolfly\GiaoHangNhanh\Model\Config;
use Magento\Checkout\Block\Checkout\DirectoryDataProcessor as MageDirectoryDataProcessor;

class DirectoryDataProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * DirectoryDataProcessor constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param MageDirectoryDataProcessor $subject
     * @param $result
     * @return mixed
     */
    public function afterProcess(MageDirectoryDataProcessor $subject, $result)
    {
        $result['components']['checkoutProvider']['dictionaries']['district'] = $this->config->getDistrictOptions();

        return $result;
    }
}
