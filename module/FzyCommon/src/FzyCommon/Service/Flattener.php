<?php
namespace FzyCommon\Service;

use FzyCommon\Entity\BaseInterface;
use Aws\S3\S3Client;

/**
 * Class Flattener
 * @package FzyCommon\Service
 * Service Key: flattener
 */
class Flattener extends Base
{
    /**
     * This service returns an entity's flatten array result
     *
     * @param  array    $data
     * @param  S3Client $s3Client
     * @param  string   $bucket
     * @return array
     */
    public function convertS3(array $data, S3Client $s3Client, $bucket)
    {
        $result = array();
        foreach ($data as $dataIndex => $dataValue) {
            if (is_array($dataValue)) {
                // recurse
                $result[$dataIndex] = $this->convertS3($dataValue, $s3Client, $bucket);
            } else {
                $result[$dataIndex] = $dataValue;
            }
        }

        return $result;
    }

    /**
     * Convert an entity into a simple PHP array for JSON encoding.
     * @param  BaseInterface $entity
     * @return array
     */
    public function flatten(BaseInterface $entity)
    {
        return $this->convertS3($entity->flatten(), $this->getServiceLocator()->get('FzyCommon\Service\Aws\S3'), $this->getServiceLocator()->get('FzyCommon\Service\Aws\S3\Config')->get('bucket'));
    }

}
