<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/15/18
 * Time: 9:56 PM
 */

namespace AppBundle\Doctrine\Processor\EntityInherit;


class EntityWrapper
{
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity->getEntity();
    }

    public function setEntity($entity)
    {
        $this->entity->setEntity($entity);
    }

    public function getParent()
    {
        return $this->entity->getParent();
    }

    public function getParentEntity()
    {
        return $this->getParent()->getEntity();
    }
}