<?php

/*
 * This file is part of a Wynd project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Mapping\ExtensionMetadataFactory;

class AnnotationConfigurationResolver
{
    /** @var ObjectManager */
    protected $om;

    /**
     * List of cached object configurations
     * leaving it static for reasons to look into
     * other listener configuration.
     *
     * @var array
     */
    private $configurations = [];

    /**
     * ExtensionMetadataFactory used to read the extension
     * metadata through the extension drivers.
     *
     * @var ExtensionMetadataFactory
     */
    private $extensionMetadataFactory = [];

    /**
     * Custom annotation reader.
     *
     * @var object
     */
    private $annotationReader;

    /**
     * AnnotationConfigurationResolver constructor.
     *
     * @param ObjectManager    $om
     * @param AnnotationReader $annotationReader
     */
    public function __construct(ObjectManager $om, Reader $annotationReader)
    {
        $this->om = $om;
        $this->annotationReader = $annotationReader;
    }

    /**
     * Get the configuration for specific object class
     * if cache driver is present it scans it also.
     *
     * @param $annotationClass
     * @param $entityClass
     *
     * @return array
     */
    public function getConfiguration($annotationClass, $entityClass)
    {
        $annotationNamespace = $annotationClass::getNamespace();
        $annotationName = $annotationClass::getName();

        $config = [];
        if (isset($this->configurations[$annotationName][$entityClass])) {
            $config = $this->configurations[$annotationName][$entityClass];
        } else {
            $factory = $this->om->getMetadataFactory();
            $cacheDriver = $factory->getCacheDriver();
            if ($cacheDriver) {
                $cacheId = ExtensionMetadataFactory::getCacheId($entityClass, $annotationNamespace);

                if (($cached = $cacheDriver->fetch($cacheId)) !== false) {
                    $this->configurations[$annotationName][$entityClass] = $cached;
                    $config = $cached;
                } else {
                    // re-generate metadata on cache miss
                    $this->loadMetadataForObjectClass($annotationNamespace, $annotationName, $factory->getMetadataFor($entityClass));
                    if (isset($this->configurations[$annotationName][$entityClass])) {
                        $config = $this->configurations[$annotationName][$entityClass];
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Scans the objects for extended annotations
     * event subscribers must subscribe to loadClassMetadata event.
     *
     * @param string $annotationNamespace
     * @param string $annotationName
     * @param object $metadata
     */
    public function loadMetadataForObjectClass(string $annotationNamespace, string $annotationName, $metadata)
    {
        $factory = $this->getExtensionMetadataFactory($this->om, $annotationNamespace);
        try {
            $config = $factory->getExtensionMetadata($metadata);
        } catch (\ReflectionException $e) {
            // entity\document generator is running
            $config = false; // will not store a cached version, to remap later
        }
        if ($config) {
            $this->configurations[$annotationName][$metadata->name] = $config;
        }
    }

    /**
     * Get extended metadata mapping reader.
     *
     * @param ObjectManager $objectManager
     * @param string        $annotationNamespace
     *
     * @return ExtensionMetadataFactory
     */
    protected function getExtensionMetadataFactory(ObjectManager $objectManager, string $annotationNamespace)
    {
        $oid = spl_object_hash($objectManager).'_'.$annotationNamespace;
        if (!isset($this->extensionMetadataFactory[$oid])) {
            $this->extensionMetadataFactory[$oid] = $this->factoryExtensionMetadataFactory($objectManager, $annotationNamespace);
        }

        return $this->extensionMetadataFactory[$oid];
    }

    /**
     * @param ObjectManager $objectManager
     * @param string        $annotationNamespace
     *
     * @return ExtensionMetadataFactory
     *
     * @codeCoverageIgnore
     */
    protected function factoryExtensionMetadataFactory(ObjectManager $objectManager, string $annotationNamespace)
    {
        return new ExtensionMetadataFactory(
            $objectManager,
            $annotationNamespace,
            $this->annotationReader
        );
    }
}