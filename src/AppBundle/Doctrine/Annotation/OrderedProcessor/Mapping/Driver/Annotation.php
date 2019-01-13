<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 11:48 PM
 */

namespace AppBundle\Doctrine\Annotation\OrderedProcessor\Mapping\Driver;


use AppBundle\Doctrine\Annotation\OrderedProcessor\OrderedProcessor;
use AppBundle\Doctrine\Annotation\SequencedCode\SequencedCode;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Mapping\Driver\AbstractAnnotationDriver;

class Annotation extends AbstractAnnotationDriver
{
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * @param ClassMetadata $meta
     * @param array $config
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        $classInspector = $meta->getReflectionClass();

        /** @var OrderedProcessor $annotation */
        $annotation = $this
            ->getReader()
            ->getClassAnnotation(
                $classInspector,
                OrderedProcessor::class
            );

        if ($annotation) {
            $config['config']['processor_sort'] = $annotation->sort;
        }
    }
}