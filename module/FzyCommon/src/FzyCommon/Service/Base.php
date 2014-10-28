<?php
namespace FzyCommon\Service;

use FzyCommon\Entity\Base\UserNull;
use FzyCommon\Util\Params;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Base
 * @package FzyCommon\Service
 */
class Base implements ServiceLocatorAwareInterface
{
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
     * @return array
     */
    public function getConfig()
    {
        if (!isset($config)) {
            $this->config = Params::create($this->getServiceLocator()->get('config'));
        }
	    return $this->config;
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
		    $this->em = $this->getServiceLocator()->get('em');
	    }
	    return $this->em;
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
        throw new \Exception('No ACL at this time');
    }

}
