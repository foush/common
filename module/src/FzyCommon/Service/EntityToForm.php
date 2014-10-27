<?php

namespace FzyCommon\Service;

use FzyCommon\Entity\BaseInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Annotation\AnnotationBuilder;

/**
 * Class EntityToForm
 * @package FzyCommon\Service
 *
 * Service Key: entity_to_form
 *
 */
class EntityToForm extends Base
{

    /**
     * @param $entity
     * @return null|\Zend\Form\Form
     */
    public function convertEntity(BaseInterface $entity)
    {
        if (empty($entity)) {
            throw new \InvalidArgumentException("Entity must not be null!");
        }
        $className = get_class($entity);
        // if passed a null object, instantiate a new doctrine object of that class
        if ($entity->isNull()) {
            // null object, get
            $className = substr($className, 0, -4);
            $entity = new $className();
        }
        $builder = new AnnotationBuilder();
        // use the entity annotations and create a zend form object
        $form = $builder->createForm($className);
        // set strategy for how to transfer data between form elements and
        $form->setHydrator(new DoctrineHydrator($this->getServiceLocator()
            ->get('em'), $className));
        // populate form with entity
        $form->bind($entity);

        return $form;
    }

    public function __invoke(BaseInterface $entity)
    {
        return $this->convertEntity($entity);
    }

}
