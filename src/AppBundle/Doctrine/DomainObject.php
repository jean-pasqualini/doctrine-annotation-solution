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

class DomainObject implements NotifyPropertyChanged
{
    /** @var UnitOfWork */
    private $unitOfWork;

    /** @var PropertyChangedListener[] */
    private $listener = [];

    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        if ($listener instanceof UnitOfWork) {
            $this->unitOfWork = $listener;
        } else {
            $this->listener[] = $listener;
        }
    }

    public function propertyChanged($property, $oldValue, $newValue)
    {
        if (!$this->unitOfWork) {
            return;
        }

        $this->unitOfWork->propertyChanged($this, $property, $oldValue, $newValue);

        foreach ($this->listener as $listener) {
            $listener->propertyChanged($this, $property, $oldValue, $newValue);
        }
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}