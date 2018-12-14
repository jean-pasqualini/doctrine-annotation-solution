<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:13 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({
 *     "AppBundle\Doctrine\Annotation\EntityInherit\EntityInheritListener",
 *     "AppBundle\Doctrine\Annotation\SequencedCode\SequencedCodeGeneratorListener"
 * })
 */
class Area
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Area")
     */
    public $parent;

    /**
     * @ORM\Column(name="title", type="string")
     */
    public $title;
}