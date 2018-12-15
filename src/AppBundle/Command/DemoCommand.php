<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:17 PM
 */

namespace AppBundle\Command;


use AppBundle\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoCommand extends Command
{
    public static $defaultName = 'app:demo';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parent = new Area();
        $parent->entity = 'Auchan';

        $area = new Area();
        $area->parent = $parent;

        $this->em->persist($area);
        $this->em->flush();

        dump($area);
    }
}