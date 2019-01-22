<?php

namespace AppBundle\Doctrine\Processor\SequencedCode;

use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Doctrine\Annotation\SequencedCode\SequencedCode;
use AppBundle\Doctrine\DomainObject;
use AppBundle\Doctrine\Processor\DoctrineProcessorInterface;

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

    public static function getNamespace()
    {
        return SequencedCode::getNamespace();
    }

    public static function getClass()
    {
        return __CLASS__;
    }

    public static function getName()
    {
        return 'sequenced_code';
    }
}