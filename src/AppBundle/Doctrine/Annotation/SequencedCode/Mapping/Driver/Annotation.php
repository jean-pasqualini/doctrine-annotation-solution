<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 11:48 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode\Mapping\Driver;


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

        foreach ($classInspector->getProperties() as $propertyInspector) {
            /** @var SequencedCode $annotation */
            $annotation = $this
                ->getReader()
                ->getPropertyAnnotation(
                    $propertyInspector,
                    SequencedCode::class
                );

            if ($annotation) {
                $codeSetter = 'set'.ucfirst($propertyInspector->name);

                if (!$classInspector->hasMethod($codeSetter)) {
                    return;
                }

                if ($annotation->tree && !$this->isTreeUsable($classInspector)) {
                    return;
                }
                $config['operations'][$annotation->tech_key] = [
                    'setter' => $codeSetter,
                    'tree' => $annotation->tree,
                ];

                return;
            }
        }
    }

    public function isTreeUsable(\ReflectionClass $classInspector)
    {
        return $classInspector->hasMethod('getParent')
            && $classInspector->hasMethod('getEntity');
    }
}