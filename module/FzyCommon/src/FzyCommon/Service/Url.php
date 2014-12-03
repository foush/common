<?php
namespace FzyCommon\Service;

/**
 * Class Url
 * @package FzyCommon\Service
 */
class Url extends Base {

    public function fromRoute($routeName, $routeParams = array(), $routeOptions = array())
    {
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $this->getServiceLocator()->get('router');
        return $router->assemble($routeParams, array_merge($routeOptions, array('name' => $routeName)));
    }
}