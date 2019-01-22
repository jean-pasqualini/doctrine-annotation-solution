<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:17 PM
 */

namespace AppBundle\Command;


use AppBundle\Entity\Area;
use AppBundle\Entity\Commande;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;

class DemoCommand extends Command
{
    public static $defaultName = 'app:demo';

    /** @var EntityManagerInterface */
    private $em;

    /** @var Factory */
    private $lockFactory;

    public function __construct(EntityManagerInterface $em, Factory $lockFactory)
    {
        $this->em = $em;
        $this->lockFactory = $lockFactory;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = $this->lockFactory->createLock('command_order_number_generator', 300);

        $lock->acquire(true);

        $last = $this->em->getRepository(Commande::class)->getLastId();

        $next = ($last) ? ++$last : 'order-000000000001';

        $order = new Commande();

        $order->orderNumber = $next;

        $this->em->persist($order);
        $this->em->flush();

        $lock->release();
    }
}