<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:13 PM
 */

namespace AppBundle\Entity;

use AppBundle\Doctrine\Annotation\EntityInherit\EntityInherit;
use AppBundle\Doctrine\Annotation\SequencedCode\SequencedCode;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\ManyToOne(targetEntity="Area", cascade={"persist"})
     */
    public $parent;

    /**
     * @SequencedCode(
     *     tree=true,
     *     comment="on génère une séquence de type AA qui est propre à chaque noeud parent/entity"
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    public $code;

    /**
     * @EntityInherit(comment="on hérite de la boutique du parent")
     */
    public $entity;

    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    public $title;

    public function getParent()
    {
        return $this->parent;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }
}