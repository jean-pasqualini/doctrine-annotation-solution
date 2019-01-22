<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 1/13/19
 * Time: 9:07 AM
 */

namespace AppBundle\Doctrine;


use AppBundle\Doctrine\Annotation\OrderedProcessor\OrderedProcessor;

class EntityProcessorOrchestrator
{
    /** @var AnnotationConfigurationResolver */
    private $configurationResolver;

    /** @var string */
    private $entityClass;

    /** @var EntityProcessorRegistry */
    private $entityProcessorRegistry;

    public function __construct(string $entityClass, EntityProcessorRegistry $entityProcessorRegistry, AnnotationConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
        $this->entityClass = $entityClass;
        $this->entityProcessorRegistry = $entityProcessorRegistry;
    }

    public function fetchOrderOperation()
    {
        $config = $this->configurationResolver->getConfiguration(OrderedProcessor::class, $this->entityClass)['config'] ?? [];

        return $config['processor_sort'];
    }

    public function fetchOperations(): array
    {
        foreach ($this->entityProcessorRegistry->getClassNames() as $entityProcessClass) {
            $config = $this->configurationResolver->getConfiguration($entityProcessClass, $this->entityClass)['operations'] ?? [];
            foreach ($config as $operationName => $operationConfig) {
                $operations[$operationName] = [
                    'processor' => $entityProcessClass,
                    'config' => $operationConfig,
                ];
            }
        }

        return $operations;
    }

    public function fetchOperationSorted($operations, $sortOperations): iterable
    {
        $operationSortedByClass = [];

        foreach ($sortOperations as $selectedOperation) {
            if (isset($operations[$selectedOperation])) {
                $operationSortedByClass[] = $operations[$selectedOperation];
            }
        }

        return $operationSortedByClass;
    }

    public function preFlush($entity)
    {
        $operations = $this->fetchOperationSorted(
            $this->fetchOperations(),
            $this->fetchOrderOperation()
        );

        foreach ($operations as $operation) {
            $this->entityProcessorRegistry->get($operation['processor'])->process($entity, $operation['config']);
        }
    }
}