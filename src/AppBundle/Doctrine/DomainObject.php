<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 3:40 PM
 */

namespace AppBundle\Doctrine;


use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\UnitOfWork;

abstract class DomainObject
{
    protected $id;

    private $propertyChanged = [];

    public function propertyChanged($property)
    {
        $this->propertyChanged[$property] = true;
    }

    public function isPropertyChanged($property)
    {
        return $this->propertyChanged[$property] ?? false;
    }

    public function isNew()
    {
        return null === $this->id;
    }

    public function getClass()
    {
        return __CLASS__;
    }
}