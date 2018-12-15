<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:13 PM
 */

namespace AppBundle\Entity;

use AppBundle\Doctrine\Annotation\EntityInherit\EntityInherit;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({
 *     "AppBundle\Doctrine\Annotation\EntityInherit\EntityInheritListener",
 *     "AppBundle\Doctrine\Annotation\SequencedCode\SequencedCodeGeneratorListener"
 * })
 * //@ORM\LifecycleSequenced("EntityInherit")
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
     * @Gedmo\TreePathSource()
     */
    public $code;

    /**
     * @Gedmo\TreePath(separator=" - ")
     */
    public $path;

    /**
     * @EntityInherit()
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
}