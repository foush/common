<?php
namespace FzyCommon\View\Helper;

class Request extends Base
{
    protected $application;

    public function __invoke()
    {
        return $this;
    }

    public function route()
    {
        return $this->getApplication()->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
    }

    public function controller()
    {
        return $this->getApplication()->getMvcEvent()->getController();
    }

    public function action()
    {
        return $this->getApplication()->getMvcEvent()->getRouteMatch()->getParam('action');
    }

    /**
     * @return \Zend\Mvc\Application
     */
    protected function getApplication()
    {
        if (!isset($this->application)) {
            $this->application = $this->getService('application');
        }

        return $this->application;
    }
}
