<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:21 PM
 */

namespace AppBundle\Doctrine\Annotation\EntityInherit;

use AppBundle\Doctrine\Annotation\MappedEventListener;
use Doctrine\Common\Persistence\ObjectManager;

class EntityInheritListener extends MappedEventListener
{
    /** @var ObjectManager */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        parent::__construct();
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getSubscribedEvents()
    {
        return [];
    }

    public function prePersist($entity)
    {
        $config = $this->getConfiguration($this->om, get_class($entity));

        dump('----',$config);
        if (!($config['entity_inherit'] ?? false)) {
            return;
        }

        if ($entity->getParent()) {
            $entity->setEntity($entity->getParent()->getEntity());
        }
    }
}