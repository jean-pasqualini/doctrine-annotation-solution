<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:22 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode;


use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Mapping\MappedEventSubscriber;

class SequencedCodeGeneratorListener extends MappedEventSubscriber
{
    /** @var ObjectManager */
    private $om;

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getSubscribedEvents()
    {
        return [];
    }

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function prePersist($entity)
    {
        $config = $this->getConfiguration($this->om, get_class($entity));

        dump($config);
    }
}