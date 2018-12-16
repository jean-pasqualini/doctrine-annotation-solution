<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:22 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode;


use AppBundle\Doctrine\Annotation\MappedEventListener;
use Doctrine\Common\Persistence\ObjectManager;

class SequencedCodeGeneratorListener extends MappedEventListener
{
    /** @var ObjectManager */
    private $om;

    /** @var bool */
    private $strict;

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getSubscribedEvents()
    {
        return [];
    }

    public function __construct(ObjectManager $om, bool $strict)
    {
        $this->om = $om;
        $this->strict = $strict;
    }

    protected function factoryEntityWrapper($entity, $config)
    {
        return new EntityWrapper($entity, $config);
    }

    public function prePersist($subject)
    {
        $this->updateCode($subject);
    }

    public function preFlush($subject)
    {
        $this->updateCode($subject);
    }

    /**
     * @param $subject
     */
    public function updateCode($subject): void
    {
        $config = $this->getConfiguration($this->om, get_class($subject));

        if (!$config) {
            return;
        }

        $entity = $this->factoryEntityWrapper($subject, $config['sequenced_code']);

        $generatedCode = ((string) $entity->getEntity()[0]);

        $entity->setCode($generatedCode);
    }
}