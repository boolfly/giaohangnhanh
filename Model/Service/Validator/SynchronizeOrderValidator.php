<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Service\Validator;

use Boolfly\GiaoHangNhanh\Model\Service\Helper\SubjectReader;
use Boolfly\GiaoHangNhanh\Model\Service\Request\AbstractDataBuilder;
use Boolfly\IntegrationBase\Model\Service\Validator\ResultInterface;

/**
 * Class SynchronizeOrderValidator
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Validator
 */
class SynchronizeOrderValidator extends AbstractResponseValidator
{
    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $errorMessages = [];
        $response = SubjectReader::readResponse($validationSubject);
        $responseData = SubjectReader::readResponseData($response);
        $validationResult = $this->validateResponseMsg($response) && $this->validatePaymentTypeId($responseData);

        if (!$validationResult) {
            $errorMessages = [__('Something went wrong when synchronize order.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * @param array $responseData
     * @return bool
     */
    private function validatePaymentTypeId(array $responseData)
    {
        return isset($responseData[AbstractDataBuilder::PAYMENT_TYPE_ID])
            && $responseData[AbstractDataBuilder::PAYMENT_TYPE_ID] == $this->config->getValue('payment_type');
    }
}
