<?php
namespace FzyCommon\Controller;

use FzyCommon\Util\Params;

/**
 * Class AbstractServiceController
 * @package FzyCommon\Controller
 */
abstract class AbstractServiceController extends AbstractController
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

}
