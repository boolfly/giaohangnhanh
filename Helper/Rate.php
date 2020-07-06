<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Helper;

use Magento\Directory\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rate
 *
 * @package Boolfly\GiaoHangNhanh\Helper
 */
class Rate
{
    /**
     * Vietnam dong currency
     */
    const CURRENCY_CODE = 'VND';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * OrderDetailsDataBuilder constructor.
     *
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
    }

    /**
     * @param float $amount
     * @return false|float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAmountByStoreCurrency($amount)
    {
        if ($this->getDefaultCurrencyCode() == self::CURRENCY_CODE) {
            return $amount;
        } else {
            try {
                return round($this->helperData->currencyConvert(
                    $amount,
                    self::CURRENCY_CODE,
                    $this->getDefaultCurrencyCode()
                ), 2);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('We can\'t convert VND to store default currency. Please setup currency rates.')
                );
            }
        }
    }

    /**
     * @param Order $order
     * @param $amount
     * @return false|float
     * @throws LocalizedException
     */
    public function getVndOrderAmount(Order $order, $amount)
    {
        if ($this->isVietnamDong($order)) {
            return $amount;
        } else {
            try {
                return round($this->helperData->currencyConvert(
                    $amount,
                    $order->getOrderCurrencyCode(),
                    self::CURRENCY_CODE
                ));
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('We can\'t convert base currency to %1. Please setup currency rates.', self::CURRENCY_CODE)
                );
            }
        }
    }

    /**
     * @param Order $order
     * @return boolean
     */
    private function isVietnamDong($order)
    {
        return $order->getOrderCurrencyCode() === self::CURRENCY_CODE;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getDefaultCurrencyCode()
    {
        return $this->storeManager->getStore()->getDefaultCurrencyCode();
    }
}
