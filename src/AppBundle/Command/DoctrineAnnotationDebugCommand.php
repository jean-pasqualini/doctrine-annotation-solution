<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/15/18
 * Time: 11:13 PM
 */

namespace AppBundle\Command;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\EntityListeners;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class DoctrineAnnotationDebugCommand extends Command
{
    public static $defaultName = 'debug:doctrine:annotation';

    /** @var ObjectManager */
    private $om;

    /** @var Reader */
    private $reader;

    public function __construct(ObjectManager $om, Reader $reader)
    {
        $this->om = $om;
        $this->reader = $reader;
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('filter', InputArgument::REQUIRED, 'filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        $style = new SymfonyStyle($input, $output);

        $classMetadataCollection = $this->om->getMetadataFactory()->getAllMetadata();

        foreach ($classMetadataCollection as $classMetadata) {
            if (strpos($classMetadata->getName(), $filter) !== false) {
                $style->title($classMetadata->getName());

                $table = new Table($output);
                $table->setHeaders(['property', 'annotation', 'Description']);

                $classInspector = $classMetadata->getReflectionClass();

                /** @var EntityListeners $annotation */
                $annotation = $this->reader->getClassAnnotation($classInspector, EntityListeners::class);

                if (!$annotation) {
                    continue;
                }

                $listeners = $annotation->value;

                $lines = [];

                foreach ($listeners as $listenerClass) {
                    foreach ($classInspector->getProperties() as $propertyInspector) {
                        /** @var Annotation $propertyAnnotation */
                        foreach ($this->reader->getPropertyAnnotations($propertyInspector) as $propertyAnnotation) {

                            $propertyAnnotationClass = explode('\\', get_class($propertyAnnotation));
                            $propertyAnnotationShortClass = end($propertyAnnotationClass);

                            if (property_exists(get_class($propertyAnnotation), 'comment') && strpos($listenerClass, $propertyAnnotationShortClass) !== false) {
                                $fqcnExploded = explode('\\', get_class($propertyAnnotation));
                                $lines [] = $propertyAnnotation->comment;
                                /**
                                $table->addRow([
                                    $propertyInspector->name,
                                    end($fqcnExploded),
                                    $propertyAnnotation->comment
                                ]);*/
                            }
                        }
                    }
                }

                $style->listing($lines);
            }
        }
    }
}