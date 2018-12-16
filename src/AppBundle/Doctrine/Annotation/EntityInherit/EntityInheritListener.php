<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:21 PM
 */

namespace AppBundle\Doctrine\Annotation\EntityInherit;

use AppBundle\Doctrine\Annotation\BadConfigurationAnnotationException;
use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Doctrine\DomainObject;
use Doctrine\Common\Persistence\ObjectManager;

class EntityInheritListener extends MappedEventListener
{
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * @param $entity
     * @throws BadConfigurationAnnotationException
     */
    public function preFlush(DomainObject $entity)
    {
        if ($entity->isNew()) {
            $config = $this->getConfiguration($entity);

            if ($this->isStrictMode()) {
                $this->throwErrorOnInvalidConfig($config);
            }

            if (!$this->isEnabled($config, 'entity_inherit')) {
                return;
            }

            $entity = new EntityWrapper($entity);

            if ($entity->getParent()) {
                $entity->setEntity($entity->getParentEntity());
            }
        }
    }
}