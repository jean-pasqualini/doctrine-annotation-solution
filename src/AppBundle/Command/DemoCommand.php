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
        $this->delete();
        $this->create();
        $this->update();
    }

    public function delete()
    {
        /** @var Area[] $areas */
        $areas = $this->em->getRepository(Area::class)->findAll();

        foreach ($areas as $area) {
            $this->em->remove($area);
        }

        $this->em->flush();
    }

    public function create()
    {

        dump('-------------> Create');
        $shop = new Area('shop');
        $shop->entity = 'Auchan';

        $firstFloor = new Area('firstFloor');
        $secondFloor = new Area('secondFloor');
        $shop->addChildren($firstFloor);
        $shop->addChildren($secondFloor);

        $rayon = new Area('rayon');
        $firstFloor->addChildren($rayon);

        $this->em->persist($shop);
        $this->em->flush();

        dump($rayon->debug());

        $this->em->clear();
    }

    public function update()
    {
        dump('-------------> Update');
        /** @var Area $rayon */
        $rayon = $this->em->getRepository(Area::class)->findOneByTitle('rayon');
        $secondFloor = $this->em->getRepository(Area::class)->findOneByTitle('secondFloor');

        $rayon->setParent($secondFloor);
        $rayon->setCode('LL');

        $this->em->flush();

        dump($rayon->debug());
    }
}