<?php

namespace FzyCommon\Controller\Plugin;

class EntityToForm extends Base
{
    public function __invoke(BaseInterface $entity)
    {
        return $this->getService('FzyCommon\Service\EntityToForm')->convertEntity($entity);
    }
}
