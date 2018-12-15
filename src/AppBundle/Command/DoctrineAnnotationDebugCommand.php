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

                foreach ($classInspector->getProperties() as $propertyInspector) {
                    foreach ($this->reader->getPropertyAnnotations($propertyInspector) as $propertyAnnotation) {
                        if (property_exists(get_class($propertyAnnotation), 'comment')) {
                            $fqcnExploded = explode('\\', get_class($propertyAnnotation));
                            $table->addRow([
                                $propertyInspector->name,
                                end($fqcnExploded),
                                $propertyAnnotation->comment
                            ]);
                        }
                    }
                }

                $table->render();
            }
        }
    }
}