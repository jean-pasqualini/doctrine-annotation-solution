<?php
/**
 * Created by PhpStorm.
 * User: jpasqualini
 * Date: 22/01/19
 * Time: 10:46
 */

namespace AppBundle\Doctrine;

class EntityProcessorRegistry
{
    private $entityProcessors = [];

    public function __construct(iterable $entityProcessors)
    {
        foreach ($entityProcessors as $entityProcessor) {
            $this->entityProcessors[$entityProcessor::getClass()] = $entityProcessor;
        }
    }

    public function get(string $className)
    {
        return $this->entityProcessors[$className] ?? null;
    }

    public function getClassNames()
    {
        return array_keys($this->entityProcessors);
    }
}