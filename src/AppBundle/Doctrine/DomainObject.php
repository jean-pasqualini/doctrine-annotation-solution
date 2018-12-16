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

class DomainObject implements NotifyPropertyChanged
{
    /** @var PropertyChangedListener */
    private $listener;

    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->listener = $listener;
    }

    public function propertyChanged($property, $oldValue, $newValue)
    {
        $this->listener->propertyChanged($this, $property, $oldValue, $newValue);
    }

    public function __set($name, $value)
    {
        if ($this->listener) {
            $this->propertyChanged($name, $this->{$name}, $value);
        }

        $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}