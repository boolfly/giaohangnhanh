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

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Authorization
 *
 * @package Boolfly\GiaoHangNhanh\Model\Service\Helper
 */
class Authorization
{
    /**
     * @var string
     */
    protected $params;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Authorization constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->params;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParameter($params)
    {
        $this->params = $this->serializer->serialize($params);
        return $this;
    }

    /**
     * @param $params
     * @return bool|string
     */
    public function getBody($params)
    {
        return $this->serializer->serialize($params);
    }

    /**
     * Get Header
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->getParameter())
        ];
    }
}
