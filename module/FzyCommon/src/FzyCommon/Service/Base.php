<?php
namespace FzyCommon\Service;

use FzyCommon\Entity\Base\UserNull;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Base
 * @package FzyCommon\Service
 */
class Base implements ServiceLocatorAwareInterface
{
    const CONFIG_KEY = 'application';

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * @var
     */
    protected $config;

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!isset($config)) {
            $this->config = $this->getServiceLocator()->get('config');
        }

        return
            isset($this->config) &&
            isset($this->config[static::CONFIG_KEY]) ?
                $this->config[static::CONFIG_KEY]:
                null;
    }

    /**
     * Get a particular config value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        $config = $this->getConfig();

        return
            isset($config[$key]) ?
                $config[$key] :
                $default;
    }

    /**
     * @param  array  $options
     * @param  string $key
     * @return null
     */
    protected function extractOption($options = array(), $key = '')
    {
        return
            isset($options[$key]) ?
                $options[$key] :
                null;
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
        if (isset($this->em))
            return $this->em;

        return $this->getServiceLocator()->get('em');
    }

    /**
     * @param \Doctrine\ORM\EntityManager$em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
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

    /**
     * @return \FzyCommon\Entity\Base\UserInterface
     */
    public function currentUser()
    {
        $auth = $this->getServiceLocator()->get('zfcuser_auth_service');

        return $auth->hasIdentity() ? $auth->getIdentity() : new UserNull();
    }

    /**
     * @param $resource
     * @param  null $privilege
     * @return bool
     */
    public function allowed($resource, $privilege = null)
    {
        /* @var $service \BjyAuthorize\Service\Authorize */

        return $this->getServiceLocator()->get('BjyAuthorize\Service\Authorize')->isAllowed($resource, $privilege);
    }

}
