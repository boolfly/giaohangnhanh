<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Model\Carrier\GHN;

use Boolfly\GiaoHangNhanh\Model\Carrier\GHN;

/**
 * Class Express
 *
 * @package Boolfly\GiaoHangNhanh\Model\Carrier\GHN
 */
class Express extends GHN
{
    const SERVICE_NAME = 'Nhanh';

    /**
     * @var string
     */
    protected $_code = 'giaohangnhanh_express';
}
