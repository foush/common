<?php

namespace FzyCommon\Controller\Plugin;

class EntityToForm extends Base
{
    public function __invoke(BaseInterface $entity)
    {
        return $this->getService('entity_to_form')->convertEntity($entity);
    }
}
