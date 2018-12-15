<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 11:48 PM
 */

namespace AppBundle\Doctrine\Annotation\EntityInherit\Mapping\Driver;


use AppBundle\Doctrine\Annotation\EntityInherit\EntityInherit;
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

        $methods = get_class_methods($classInspector->name);

        $requiredMethods = ['getParent', 'getEntity', 'setEntity'];

        foreach ($requiredMethods as $requiredMethod) {
            if (!in_array($requiredMethod, $methods)) {
                return;
            }
        }

        foreach ($classInspector->getProperties() as $propertyInspector) {
            $annotation = $this
                ->getReader()
                ->getPropertyAnnotation(
                    $propertyInspector,
                    EntityInherit::class
                );

            if ($annotation) {
                $config['entity_inherit'] = true;
                return;
            }
        }
    }
}