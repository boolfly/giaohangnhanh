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

use Boolfly\GiaoHangNhanh\Model\Config;
use Boolfly\GiaoHangNhanh\Model\Service\Helper\SubjectReader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SynchronizeOrderDataBuilder
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Request
 */
class SynchronizeOrderDataBuilder extends AbstractDataBuilder
{
    /**
     * @param array $buildSubject
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $order = SubjectReader::readOrder($buildSubject);
        $weightRate = $this->baseConfig->getWeightUnit() == self::DEFAULT_WEIGHT_UNIT ? Config::KGS_G : Config::LBS_G;
        $store = $this->storeManager->getStore();
        $storeInfo = $this->storeInformation->getStoreInformationObject($store);
        $storeFormattedAddress = $this->storeInformation->getFormattedAddress($store);
        $storeDistrict = (int)$this->config->getValue('district');

        return [
            self::TOKEN => $this->config->getValue('api_token'),
            self::PAYMENT_TYPE_ID => (int)$this->config->getValue('payment_type'),
            self::FROM_DISTRICT_ID => $storeDistrict,
            self::TO_DISTRICT_ID => (int)SubjectReader::readDistrict($buildSubject),
            self::CLIENT_CONTACT_NAME => $storeInfo->getName(),
            self::CLIENT_CONTACT_PHONE => $storeInfo->getPhone(),
            self::CLIENT_ADDRESS => $storeFormattedAddress,
            self::CUSTOMER_NAME => $order->getCustomerName(),
            self::CUSTOMER_PHONE => $order->getShippingAddress()->getTelephone(),
            self::SHIPPING_ADDRESS => $order->getShippingAddress()->getStreetLine(1),
            self::NOTE_CODE => $this->config->getValue('note_code'),
            self::SERVICE_ID => (int)SubjectReader::readShippingServiceId($buildSubject),
            self::WEIGHT => $order->getWeight() * $weightRate,
            self::LENGTH => (int)$this->config->getValue('default_length'),
            self::WIDTH => (int)$this->config->getValue('default_width'),
            self::HEIGHT => (int)$this->config->getValue('default_height'),
            self::CO_D_AMOUNT => $this->helperRate->getVndOrderAmount($order, $order->getGrandTotal()),
            self::RETURN_CONTACT_NAME => $storeInfo->getName(),
            self::RETURN_CONTACT_PHONE => $storeInfo->getPhone(),
            self::RETURN_ADDRESS => $storeFormattedAddress,
            self::RETURN_DISTRICT_ID => $storeDistrict,
            self::EXTERNAL_RETURN_CODE => $storeInfo->getName()
        ];
    }
}
