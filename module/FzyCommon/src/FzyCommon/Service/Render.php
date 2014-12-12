<?php
namespace FzyCommon\Service;

use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;

/**
 * Class Render
 * @package FzyCommon\Service
 *
 * Service Key: FzyCommon\Render
 */
class Render extends Base
{
    /**
     * @param $partial
     * @param  array $values
     * @return mixed
     */
    public function handle($partial, $values = array())
    {
        $view = new ViewModel($values);
        $view->setTemplate($partial)->setTerminal(true);

        return $this->handleView($view);
    }

    /**
     * @param  ModelInterface $view
     * @return mixed
     */
    public function handleView(ModelInterface $view)
    {
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');

        return $viewRender->render($view);
    }
}
