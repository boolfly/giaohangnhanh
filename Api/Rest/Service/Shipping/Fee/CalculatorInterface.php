<?php

namespace Boolfly\GiaoHangNhanh\Api\Rest\Service\Shipping\Fee;

interface CalculatorInterface
{
    /**
     * @param array $request
     *
     * @return float|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function calculate($request);
}
