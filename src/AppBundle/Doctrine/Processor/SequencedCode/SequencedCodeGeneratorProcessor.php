<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:22 PM
 */

namespace AppBundle\Doctrine\Processor\SequencedCode;


use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Doctrine\DomainObject;
use AppBundle\Doctrine\Processor\DoctrineProcessorInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SequencedCodeGeneratorProcessor implements DoctrineProcessorInterface
{
    /**
     * @param $subject
     * @return bool
     */
    protected function isParentNodeChange(DomainObject $subject): bool
    {
        return $subject->isPropertyChanged('parent');
    }

    protected function isNew(DomainObject $subject): bool
    {
        return $subject->isNew();
    }

    /**
     * @param $subject
     * @return mixed
     */
    protected function isCodeAlreadyUpdated(DomainObject $subject)
    {
        return $subject->isPropertyChanged('code');
    }

    public function getAnnotationNamespace(): string
    {
        return 'AppBundle\Doctrine\Annotation\SequencedCode';
    }

    protected function factoryEntityWrapper($entity, $config)
    {
        return new EntityWrapper($entity, $config);
    }

    public function process(DomainObject $subject, array $config)
    {
        if ($this->isCodeAlreadyUpdated($subject)) {
            return;
        }

        if ($this->isNew($subject) || $this->isParentNodeChange($subject)) {
            $entity = $this->factoryEntityWrapper($subject, $config);

            $generatedCode = ((string) $entity->getEntity()[0]);

            $entity->setCode($generatedCode);
        }
    }
}