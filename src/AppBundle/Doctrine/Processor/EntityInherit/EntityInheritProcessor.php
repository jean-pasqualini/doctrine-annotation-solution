<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 1/13/19
 * Time: 10:11 AM
 */

namespace AppBundle\Doctrine\Processor\EntityInherit;


use AppBundle\Doctrine\Annotation\EntityInherit\EntityInherit;
use AppBundle\Doctrine\DomainObject;
use AppBundle\Doctrine\Processor\DoctrineProcessorInterface;

class EntityInheritProcessor implements DoctrineProcessorInterface
{
    public function process(DomainObject $entity, array $config)
    {
        if ($entity->isNew()) {
            $entity = new EntityWrapper($entity);

            if ($entity->getParent()) {
                $entity->setEntity($entity->getParentEntity());
            }
        }
    }

    public static function getNamespace()
    {
        return EntityInherit::getNamespace();
    }

    public static function getClass()
    {
        return __CLASS__;
    }

    public static function getName()
    {
        return 'entity_inherit';
    }
}