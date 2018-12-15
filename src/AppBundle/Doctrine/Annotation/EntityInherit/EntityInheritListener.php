<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:21 PM
 */

namespace AppBundle\Doctrine\Annotation\EntityInherit;

use AppBundle\Doctrine\Annotation\BadConfigurationAnnotationException;
use AppBundle\Doctrine\Annotation\MappedEventListener;
use Doctrine\Common\Persistence\ObjectManager;

class EntityInheritListener extends MappedEventListener
{
    /** @var ObjectManager */
    private $om;

    /** @var bool */
    private $strict;

    public function __construct(ObjectManager $om, bool $strict)
    {
        $this->om = $om;
        $this->strict = $strict;
        parent::__construct();
    }

    /**
     * @param $config
     * @throws BadConfigurationAnnotationException
     */
    public function throwErrorOnInvalidConfig($config): void
    {
        if ($config['error'] ?? false) {
            throw new BadConfigurationAnnotationException($config['error']);
        }
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getSubscribedEvents()
    {
        return [];
    }

    /**
     * @param $entity
     * @throws BadConfigurationAnnotationException
     */
    public function prePersist($entity)
    {
        $config = $this->getConfiguration($this->om, get_class($entity));

        if ($this->strict) {
            $this->throwErrorOnInvalidConfig($config);
        }

        if (!($config['entity_inherit'] ?? false)) {
            return;
        }

        $entity = new EntityWrapper($entity);

        if ($entity->getParent()) {
            $entity->setEntity($entity->getParentEntity());
        }
    }
}