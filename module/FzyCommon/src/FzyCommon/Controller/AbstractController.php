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
    /**
     * @return Params
     */
    protected function getParamsFromRequest()
    {
        return Params::create($this->params(), $this->getRequest());
    }
}
