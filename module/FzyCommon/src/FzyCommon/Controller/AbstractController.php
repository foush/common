<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace FzyCommon\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use FzyCommon\Util\Params;

abstract class AbstractController extends AbstractActionController
{
    abstract protected function getSearchServiceKey();

    abstract protected function getUpdateServiceKey();

    /**
     * @return \FzyCommon\Service\Search\Base
     */
    protected function getSearchService(Params $params)
    {
        return $this->getServiceLocator()->get($this->getSearchServiceKey());
    }

    /**
     * @return \FzyCommon\Service\Update\Base
     */
    protected function getUpdateService(Params $params)
    {
        $service = $this->getServiceLocator()->get($this->getUpdateServiceKey());
        $service->setMainEntityFromParam($params, $this->getSearchService($params));

        return $service;
    }

    /**
     * @return Params
     */
    protected function getParamsFromRequest()
    {
        return Params::create($this->params(), $this->getRequest());
    }
}
