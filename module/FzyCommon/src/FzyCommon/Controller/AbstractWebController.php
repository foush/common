<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace FzyCommon\Controller;

use FzyCommon\Util\Params;
use Zend\View\Model\ViewModel;

abstract class AbstractWebController extends AbstractController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $params = Params::create($this->params(), $this->getRequest());
        $service = $this->getUpdateService($params);

        return new ViewModel(array(
            'form' => $service->form(),
            'entity' => $service->entity(),
        ));
    }
}
