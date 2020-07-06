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
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ShippingDetailsDataBuilder
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Request
 */
class ShippingDetailsDataBuilder extends AbstractDataBuilder
{
    /**
     * @param array $buildSubject
     * @return array
     * @throws NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $rateRequest = SubjectReader::readRateRequest($buildSubject);
        $rate = $this->baseConfig->getWeightUnit() == self::DEFAULT_WEIGHT_UNIT ? Config::KGS_G : Config::LBS_G;
        $data = [
            self::TOKEN => $this->config->getValue('api_token'),
            self::WEIGHT => $rateRequest->getPackageWeight() * $rate,
            self::FROM_DISTRICT_ID => (int)$this->config->getValue('district'),
            self::TO_DISTRICT_ID => (int)$rateRequest->getDistrict()
        ];

        if ($serviceId = SubjectReader::readServiceId($buildSubject)) {
            $data[self::SERVICE_ID] = $serviceId;
        }
        return $data;
    }
}
