<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:22 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode;


use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Doctrine\DomainObject;
use Doctrine\Common\Persistence\ObjectManager;

class SequencedCodeGeneratorListener extends MappedEventListener
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

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    protected function factoryEntityWrapper($entity, $config)
    {
        return new EntityWrapper($entity, $config);
    }

    public function preFlush($subject)
    {
        if ($this->isCodeAlreadyUpdated($subject)) {
            return;
        }

        if ($this->isNew($subject) || $this->isParentNodeChange($subject)) {

            $config = $this->getConfiguration($subject);

            if (!$this->isEnabled($config, 'sequenced_code')) {
                return;
            }

            $entity = $this->factoryEntityWrapper($subject, $config['sequenced_code']);

            $generatedCode = ((string) $entity->getEntity()[0]);

            $entity->setCode($generatedCode);
        }
    }
}