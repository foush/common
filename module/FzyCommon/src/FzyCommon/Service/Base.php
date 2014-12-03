<?php
namespace FzyCommon\Service;

use FzyCommon\Util\Params;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Base
 * @package FzyCommon\Service
 */
abstract class Base implements ServiceLocatorAwareInterface
{
    const MODULE_CONFIG_KEY = 'fzycommon';

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * @var Params
     */
    protected $config;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Get the application config as a Params object
     * @return Params
     */
    public function getConfig()
    {
        if (!isset($config)) {
            $this->config = $this->getServiceLocator()->get('FzyCommon\Config');
        }

        return $this->config;
    }

    /**
     * Get the module config (application config section in the module key namespace specified by static::MODULE_CONFIG_KEY)
     * @return Params
     */
    public function getModuleConfig()
    {
        return $this->getConfig()->getWrapped(static::MODULE_CONFIG_KEY);
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->locator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->locator;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        if (!isset($this->em)) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }

    /**
     * @return \FzyCommon\Service\Url
     */
    public function url()
    {
        return $this->getServiceLocator()->get('FzyCommon\Service\Url');
    }

    /**
     * @param $className
     * @param $id
     * @return \FzyCommon\Entity\BaseInterface
     * @throws \RuntimeException
     */
    public function lookup($className, $id)
    {
        $entity = !empty($id) ? $this->em()->find($className, $id) : null;

        $nullClass = $className.'Null';
        if ($className{0} != '\\') {
            $nullClass = '\\'.$nullClass;
        }
        if ($entity == null) {
            if (!class_exists($nullClass)) {
                throw new \RuntimeException("$nullClass does not exist");
            }
            $entity = new $nullClass();
        }

        return $entity;
    }

}
