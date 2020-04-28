<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Model\Api\Rest\Helper;

class ResponseReader implements ResponseReaderInterface
{
    /**
     * @var string|null
     */
    private $property;

    /**
     * ResponseReader constructor.
     * @param null $property
     */
    public function __construct($property = null)
    {
        $this->property = $property;
    }

    /**
     * @param array $response
     * @return mixed
     */
    public function read(array $response)
    {
        if (!$response) {
            return null;
        }

        $responseData = $response['data'];

        if (null === $this->property) {
            return $responseData;
        } else {
            if (empty($responseData[$this->property])) {
                throw new \InvalidArgumentException($this->property . ' ' . 'should be provided');
            }

            return $responseData[$this->property];
        }
    }
}
