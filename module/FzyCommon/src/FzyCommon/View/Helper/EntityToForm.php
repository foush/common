<?php
namespace FzyCommon\View\Helper;

use FzyCommon\Entity\BaseInterface;

class EntityToForm extends Base
{
    public function __invoke(BaseInterface $entity)
    {
        return $this->getService('FzyCommon\Service\EntityToForm')->convertEntity($entity);
    }
}
