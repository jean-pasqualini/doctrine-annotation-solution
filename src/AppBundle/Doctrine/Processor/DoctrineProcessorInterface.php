<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 1/13/19
 * Time: 9:39 AM
 */

namespace AppBundle\Doctrine\Processor;


use AppBundle\Doctrine\DomainObject;

interface DoctrineProcessorInterface
{
    public function getAnnotationNamespace(): string;

    public function process(DomainObject $object, array $configuration);
}