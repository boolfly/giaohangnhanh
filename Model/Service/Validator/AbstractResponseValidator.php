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

use Boolfly\IntegrationBase\Model\Service\ConfigInterface;
use Boolfly\IntegrationBase\Model\Service\Validator\AbstractValidator;
use Boolfly\IntegrationBase\Model\Service\Validator\ResultInterfaceFactory;

/**
 * Class AbstractResponseValidator
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Validator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{
    const MSG = 'msg';
    const SUCCESS_MESSAGE = 'Success';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * AbstractResponseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param ConfigInterface|null $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config = null
    ) {
        parent::__construct($resultFactory);
        $this->config = $config;
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateResponseMsg(array $response)
    {
        return isset($response[self::MSG]) && $response[self::MSG] === self::SUCCESS_MESSAGE;
    }
}
