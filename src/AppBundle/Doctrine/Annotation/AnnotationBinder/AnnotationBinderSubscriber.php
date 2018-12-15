<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:21 PM
 */

namespace AppBundle\Doctrine\Annotation\AnnotationBinder;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Gedmo\Mapping\MappedEventSubscriber;
use Symfony\Component\HttpKernel\KernelInterface;

class AnnotationBinderSubscriber extends MappedEventSubscriber
{
    /** @var ObjectManager */
    private $om;

    /** @var string */
    private $class;

    private $sequence;

    public function __construct(string $class, ObjectManager $om, array $sequence)
    {
        $this->class = $class;
        $this->om = $om;
        $this->sequence = $sequence;
        parent::__construct();
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist'
        ];
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $config = $this->getConfiguration($this->om, get_class($eventArgs->getEntity()));

    }
}