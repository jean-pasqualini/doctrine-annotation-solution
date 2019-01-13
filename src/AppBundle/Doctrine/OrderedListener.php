<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 1/13/19
 * Time: 9:07 AM
 */

namespace AppBundle\Doctrine;


use AppBundle\Doctrine\Processor\EntityInherit\EntityInheritProcessor;
use AppBundle\Doctrine\Processor\SequencedCode\SequencedCodeGeneratorProcessor;
use AppBundle\Doctrine\Processor\DoctrineProcessorInterface;
use AppBundle\Entity\Area;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

class OrderedListener implements EventSubscriber
{
    /** @var ConfigurationResolver */
    private $configurationResolver;

    /** @var DoctrineProcessorInterface[] */
    private $entityProcessor;

    public function __construct(iterable $entityProcessors, ConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
        foreach ($entityProcessors as $entityProcessor) {
            $this->entityProcessor[get_class($entityProcessor)] = $entityProcessor;
        }

        $this->entityProcessor[SequencedCodeGeneratorProcessor::class] = new SequencedCodeGeneratorProcessor();
        $this->entityProcessor[EntityInheritProcessor::class] = new EntityInheritProcessor();
    }

    public function getSubscribedEvents()
    {
        return [Events::preFlush];
    }

    public function fetchClassList(): array
    {
        return [Area::class];
    }

    public function fetchOrderOperation()
    {
        foreach ($this->fetchClassList() as $class) {
            $this->configurationResolver->init('AppBundle\Doctrine\Annotation\OrderedProcessor');
            $config = $this->configurationResolver->getConfiguration($class)['config'] ?? [];

            return $config['processor_sort'];
        }
    }

    public function fetchOperations(): array
    {
        $classList = [Area::class];

        foreach ($classList as $class) {

            $operations = [];

            foreach ($this->entityProcessor as $entityProcessor) {
                $this->configurationResolver->init($entityProcessor->getAnnotationNamespace());
                $config = $this->configurationResolver->getConfiguration($class)['config'] ?? [];

                foreach ($config as $operationName => $operationConfig) {
                    $operations[$operationName] = [
                        'processor' => get_class($entityProcessor),
                        'config' => $operationConfig,
                    ];
                }
            }
        }

        return $operations;
    }

    public function fetchInsertedEntities(UnitOfWork $unitOfWork): iterable
    {
        $entities = $unitOfWork->getScheduledEntityInsertions();

        foreach ($entities as $entity) {
            yield $entity;
        }
    }

    public function fetchUpdatedEntities(UnitOfWork $unitOfWork): iterable
    {
        $entitiesByClass = $unitOfWork->getIdentityMap();

        foreach ($entitiesByClass as $class => $entities) {
            foreach ($entities as $entity) {
                yield $entity;
            }
        }
    }

    public function preFlush(PreFlushEventArgs $eventArgs) {

        $operations = $this->fetchOperations();
        $sortOperations = $this->fetchOrderOperation();

        dump($sortOperations);

        $unitOfWork = $eventArgs->getEntityManager()->getUnitOfWork();

        foreach ($this->fetchInsertedEntities($unitOfWork) as $entity) {
            foreach ($sortOperations as $selectedOperation) {
                if (isset($operations[$selectedOperation])) {
                    $this->entityProcessor[$operations[$selectedOperation]['processor']]->process($entity, $operations[$selectedOperation]['config']);
                }
            }
        }

        foreach ($this->fetchUpdatedEntities($unitOfWork) as $entity) {
            foreach ($operations as $operation) {
                $this->entityProcessor[$operation['processor']]->process($entity, $operation['config']);
            }
        }

        // By class
        // On récupère l'ordre les traitement à faire
    }
}