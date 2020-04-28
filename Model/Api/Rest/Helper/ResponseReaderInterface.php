<?php

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Helper;

interface ResponseReaderInterface
{
    /**
     * @param array $response
     * @return mixed
     */
    public function read(array $response);
}
