<?php
namespace FzyCommon\View\Helper;

use FzyCommon\Entity\BaseInterface;

class EntityToForm extends Base
{
    public function __invoke(BaseInterface $entity)
    {
        return $this->getService('entity_to_form')->convertEntity($entity);
    }
}
