<?php

namespace FzyCommon\Entity\Base;

interface S3FileInterface
{
    const S3_KEY = '__s3';
    const S3_KEYS_INDEX = 'keys';
    const S3_URLS_INDEX = 'urls';

    /**
     * Returns a non-empty array of S3 keys to be translated
     * @return array
     */
    public function getS3Keys();

    /**
     * Returns a non-empty array of key names where the resulting S3 keys can be
     * set on a flattened object
     * @return array
     */
    public function getS3UrlKeys();

}
