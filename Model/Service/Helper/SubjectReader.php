<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Service\Helper;

use InvalidArgumentException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class SubjectReader
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Helper
 */
class SubjectReader extends \Boolfly\IntegrationBase\Model\Service\SubjectReader
{
    /**
     * Reads rate request from subject
     *
     * @param array $subject
     * @return RateRequest
     */
    public static function readRateRequest(array $subject)
    {
        if (empty($subject['rate_request'])
            || !$subject['rate_request'] instanceof RateRequest
        ) {
            throw new InvalidArgumentException('Rate request object should be provided');
        }

        return $subject['rate_request'];
    }

    /**
     * Reads services from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readServices(array $subject)
    {
        if (!isset($subject['services']) || !is_array($subject['services'])) {
            throw new \InvalidArgumentException('Services do not exist');
        }

        return $subject['services'];
    }

    /**
     * Reads rate from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readRate(array $subject)
    {
        if (!isset($subject['rate'])) {
            throw new \InvalidArgumentException('Rate does not exist');
        }

        return $subject['rate'];
    }

    /**
     * Reads calculated fee from subject
     *
     * @param array $subject
     * @return float
     */
    public static function readCalculatedFee(array $subject)
    {
        if (!isset($subject['CalculatedFee'])) {
            throw new \InvalidArgumentException('Calculated fee does not exist');
        }

        return (float)$subject['CalculatedFee'];
    }

    /**
     * Reads tracking code from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readTrackingCode(array $subject)
    {
        if (!isset($subject['tracking_code'])) {
            throw new \InvalidArgumentException('Tracking code does not exist');
        }

        return $subject['tracking_code'];
    }

    /**
     * Read service ID from transaction subject
     *
     * @param array $subject
     * @return string
     */
    public static function readServiceId(array $subject)
    {
        if (!empty($subject['service_id'])) {
            return $subject['service_id'];
        }

        return '';
    }

    /**
     * Reads service name from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readServiceName(array $subject)
    {
        if (empty($subject['Name'])) {
            throw new \InvalidArgumentException('Service name does not exist');
        }

        return $subject['Name'];
    }

    /**
     * Reads order from subject
     *
     * @param array $subject
     * @return OrderInterface
     */
    public static function readOrder(array $subject)
    {
        if (!isset($subject['order'])
            || !$subject['order'] instanceof OrderInterface
        ) {
            throw new \InvalidArgumentException('Order data object should be provided');
        }

        return $subject['order'];
    }

    /**
     * Reads order info from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readInfo(array $subject)
    {
        if (!isset($subject['order_info']) || !is_array($subject['order_info'])) {
            throw new \InvalidArgumentException('Order\'s info does not exist');
        }

        return $subject['order_info'];
    }

    /**
     * Reads current order status from subject
     *
     * @param array $subject
     * @return string
     */
    public static function readCurrentOrderStatus(array $subject)
    {
        if (empty($subject['CurrentStatus'])) {
            throw new \InvalidArgumentException('Order status does not exist');
        }

        return $subject['CurrentStatus'];
    }

    /**
     * Reads district from subject
     *
     * @param array $subject
     * @return string
     */
    public static function readDistrict(array $subject)
    {
        if (empty($subject['district'])) {
            throw new \InvalidArgumentException('District should be provided');
        }

        return $subject['district'];
    }

    /**
     * Reads shipping service id from subject
     *
     * @param array $subject
     * @return string
     */
    public static function readShippingServiceId(array $subject)
    {
        if (empty($subject['shipping_service_id'])) {
            throw new \InvalidArgumentException('Shipping service id should be provided');
        }

        return $subject['shipping_service_id'];
    }

    /**
     * Reads order code from subject
     *
     * @param array $subject
     * @return string
     */
    public static function readOrderCode(array $subject)
    {
        if (empty($subject['OrderCode'])) {
            throw new \InvalidArgumentException('Order code should be provided');
        }

        return $subject['OrderCode'];
    }

    /**
     * Reads response data from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readResponseData(array $subject)
    {
        if (empty($subject['data'])) {
            throw new \InvalidArgumentException('Response data should be provided');
        }

        return $subject['data'];
    }

    /**
     * Reads districts from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readDistricts(array $subject)
    {
        if (!isset($subject['districts'])) {
            throw new \InvalidArgumentException('Districts does not exist');
        }

        return $subject['districts'];
    }
}
