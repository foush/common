<?php
namespace FzyCommon\Service;

/**
 * Class Url
 * @package FzyCommon\Service
 * Service Key: FzyCommon\Url
 */
class Url extends Base
{
    /**
     * @param $routeName
     * @param  array  $routeParams
     * @param  array  $routeOptions
     * @return string
     */
    public function fromRoute($routeName, $routeParams = array(), $routeOptions = array())
    {
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $this->getServiceLocator()->get('router');

        return $router->assemble($routeParams, array_merge($routeOptions, array('name' => $routeName)));
    }

    /**
     * Returns the S3 URL for the file at the specified key.
     * expiration determines how long the URL will work
     * downlaodAs specifies what the file name should start as when downloading
     * @param $key
     * @param  null   $expiration
     * @param  null   $downloadAs
     * @return string
     */
    public function fromS3($key, $expiration = null, $downloadAs = null)
    {
        $args = array();
        if (!empty($downloadAs)) {
            if (empty($expiration)) {
                throw new \RuntimeException("Unable to set download name on URL without expiration.");
            }
            $args['ResponseContentDisposition'] = 'attachment;filename='.$downloadAs;
        }

        return $this->getServiceLocator()->get('FzyCommon\Service\Aws\S3')->getObjectUrl($this->getServiceLocator()->get('FzyCommon\Service\Aws\S3\Config')->get('bucket'), $key, $expiration, $args);
    }
}
