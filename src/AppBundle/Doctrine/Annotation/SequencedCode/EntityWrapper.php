<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/15/18
 * Time: 9:56 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode;


class EntityWrapper
{
    private $setter;

    private $entity;

    private $tree;

    public function __construct($entity, $config)
    {
        $this->entity = $entity;
        $this->setter = $config['setter'];
        $this->tree = $config['tree'];
    }

    public function setCode($code)
    {
        $this->entity->code = $code;
    }

    public function getEntity()
    {
        if (!$this->tree){
            throw $this->createNotSupportTreeException();
        }

        return $this->entity->getEntity();
    }

    public function getParent()
    {
        if (!$this->tree){
            throw $this->createNotSupportTreeException();
        }

        return $this->entity->getParent();
    }

    protected function createNotSupportTreeException()
    {
        return new \BadMethodCallException(sprintf(
            'sequenced code generator not support tree mode on %s',
            get_class($this->entity)
        ));
    }
}