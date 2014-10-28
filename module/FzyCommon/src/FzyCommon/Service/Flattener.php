<?php
namespace FzyCommon\Service;

use FzyCommon\Entity\BaseInterface;
use FzyCommon\Entity\Base\S3FileInterface;
use Aws\S3\S3Client;
use FzyCommon\Util\Params;

/**
 * Class Flattener
 * @package FzyCommon\Service
 * Service Key: flattener
 */
class Flattener extends Base
{
    /**
     * This service returns an entity's flatten array result while hooking into S3FileInterface reserved array keys,
     * transforming the S3 key into a URL and removing the reserved key
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
            if ($dataIndex === S3FileInterface::S3_KEY) {
                foreach ($dataValue[S3FileInterface::S3_KEYS_INDEX] as $index => $s3key) {
                    $result[$dataValue[S3FileInterface::S3_URLS_INDEX][$index]] = empty($s3key) ? null : $s3Client->getObjectUrl($bucket, $s3key, '+5 minutes');
                }
            } elseif (is_array($dataValue)) {
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
        return $this->convertS3($entity->flatten(), $this->getServiceLocator()->get('s3'), $this->getServiceLocator()->get('s3_config')->get('bucket'));
    }

}
