<?php
namespace FzyCommon\Entity\Base;

use FzyCommon\Entity\BaseInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

interface ServiceAwareEntityInterface extends BaseInterface, ServiceLocatorAwareInterface
{
}
