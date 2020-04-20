<?php

namespace Boolfly\GiaoHangNhanh\Api\Rest\Service;

interface DistrictProviderInterface
{
    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function getDistrictList();
}
