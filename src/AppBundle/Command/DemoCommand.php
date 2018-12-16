<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:17 PM
 */

namespace AppBundle\Command;


use AppBundle\Entity\Area;
use Doctrine\DBAL\Logging\EchoSQLLogger;
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
        $this->em->getConfiguration()->setSQLLogger(new EchoSQLLogger());

        $shop = new Area('shop');
        $shop->entity = 'Auchan';

        $firstFloor = new Area('firstFloor');
        $firstFloor->parent = $shop;
        $shop->children->add($firstFloor);

        $secondFloor = new Area('secondFloor');
        $secondFloor->parent = $shop;
        $shop->children->add($secondFloor);

        $rayon = new Area('rayon');
        $rayon->parent = $firstFloor;
        $firstFloor->children->add($rayon);

        $this->em->persist($shop);
        $this->em->flush();

        dump($rayon->debug());

        $rayonId = $rayon->id;

        $this->em->clear();

        $rayon = $this->em->getRepository(Area::class)->find($rayonId);

        $shop->entity = 'Maison';

        $rayon->parent = $secondFloor;
        $firstFloor->children->removeElement($rayon);
        $secondFloor->children->add($rayon);

        $this->em->flush();

        dump($rayon->debug());
    }
}