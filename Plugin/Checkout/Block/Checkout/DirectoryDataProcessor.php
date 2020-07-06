<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout;

use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\GiaoHangNhanh\Setup\Patch\Data\AddressAttribute;
use Magento\Checkout\Block\Checkout\DirectoryDataProcessor as MageDirectoryDataProcessor;

/**
 * Class DirectoryDataProcessor
 *
 * @package Boolfly\GiaoHangNhanh\Plugin\Checkout\Block\Checkout
 */
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
        $result['components']['checkoutProvider']['dictionaries'][AddressAttribute::DISTRICT] = $this->config->getDistrictOptions();

        return $result;
    }
}
