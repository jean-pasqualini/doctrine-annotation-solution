<?php
/**
 * Created by PhpStorm.
 * User: jpasqualini
 * Date: 21/01/19
 * Time: 11:23
 */

namespace AppBundle\Entity\Repository;


use Doctrine\ORM\EntityRepository;

class CommandeRepository extends EntityRepository
{
    public function getLastId()
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.orderNumber')
            ->orderBy('c.orderNumber', 'desc')
            ->setMaxResults(1)
            ->getQuery()->getScalarResult();

        return (!empty($result)) ? $result[0]['orderNumber']: null;
    }
}