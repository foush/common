<?php
namespace FzyCommon\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use FzyCommon\Entity\Base\ServiceAwareEntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceAwareEntity implements EventSubscriber
{
    private $serviceManager;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::postLoad,);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ServiceAwareEntityInterface) {
            $entity->setServiceLocator($this->serviceManager);
        }
    }
}
