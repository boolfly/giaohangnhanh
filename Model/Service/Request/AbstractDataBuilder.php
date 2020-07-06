<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Service\Request;

use Boolfly\GiaoHangNhanh\Helper\Rate;
use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\IntegrationBase\Model\Service\ConfigInterface;
use Boolfly\IntegrationBase\Model\Service\Request\BuilderInterface;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractDataBuilder
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    const DEFAULT_WEIGHT_UNIT = 'kgs';
    const TOKEN = 'token';
    const ORDER_CODE = 'OrderCode';
    const WEIGHT = 'Weight';
    const FROM_DISTRICT_ID = 'FromDistrictID';
    const TO_DISTRICT_ID = 'ToDistrictID';
    const PAYMENT_TYPE_ID = 'PaymentTypeID';
    const CLIENT_CONTACT_NAME = 'ClientContactName';
    const CLIENT_CONTACT_PHONE = 'ClientContactPhone';
    const CLIENT_ADDRESS = 'ClientAddress';
    const CUSTOMER_NAME = 'CustomerName';
    const CUSTOMER_PHONE = 'CustomerPhone';
    const SHIPPING_ADDRESS = 'ShippingAddress';
    const NOTE_CODE = 'NoteCode';
    const SERVICE_ID = 'ServiceID';
    const LENGTH = 'Length';
    const WIDTH = 'Width';
    const HEIGHT = 'Height';
    const CO_D_AMOUNT = 'CoDAmount';
    const RETURN_CONTACT_NAME = 'ReturnContactName';
    const RETURN_CONTACT_PHONE = 'ReturnContactPhone';
    const RETURN_ADDRESS = 'ReturnAddress';
    const RETURN_DISTRICT_ID = 'ReturnDistrictID';
    const EXTERNAL_RETURN_CODE = 'ExternalReturnCode';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Information
     */
    protected $storeInformation;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var Config
     */
    protected $baseConfig;

    /**
     * @var Rate
     */
    protected $helperRate;

    /**
     * AbstractDataBuilder constructor.
     * @param ConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param Information $storeInformation
     * @param AddressFactory $addressFactory
     * @param Config $baseConfig
     * @param Rate $helperRate
     */
    public function __construct(
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        Information $storeInformation,
        AddressFactory $addressFactory,
        Config $baseConfig,
        Rate $helperRate
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->storeInformation = $storeInformation;
        $this->addressFactory = $addressFactory;
        $this->baseConfig = $baseConfig;
        $this->helperRate = $helperRate;
    }
}
