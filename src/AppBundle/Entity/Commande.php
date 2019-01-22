<?php
/**
 * Created by PhpStorm.
 * User: jpasqualini
 * Date: 21/01/19
 * Time: 10:37
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Order
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    public $id;

    /**
     * @var string
     * @ORM\Column(name="order_number", type="string", nullable=true)
     */
    public $orderNumber;
}