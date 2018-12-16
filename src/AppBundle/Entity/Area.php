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
use AppBundle\Doctrine\DomainObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({
 *     "AppBundle\Doctrine\Annotation\EntityInherit\EntityInheritListener",
 *     "AppBundle\Doctrine\Annotation\SequencedCode\SequencedCodeGeneratorListener",
 *     "AppBundle\Doctrine\Annotation\TreePath\TreePathListener"
 * })
 */
class Area extends DomainObject
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="children", cascade={"persist"})
     */
    public $parent;

    /**
     * @ORM\OneToMany(targetEntity="Area", mappedBy="parent", cascade={"persist"})
     */
    public $children;

    /**
     * @SequencedCode(
     *     tree=true,
     *     comment="on génère une séquence de type AA qui est propre à chaque noeud parent/entity"
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @EntityInherit(comment="on hérite de la boutique du parent")
     */
    public $entity;

    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    public $title;

    /**
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    protected $path;

    public function __construct($title = null)
    {
        $this->title = $title;
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?Area
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

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function debug()
    {
        return [
            'title' => $this->title,
            'entity' => $this->entity,
            'code' => $this->code,
            'path' => $this->path,
            'parent' => ($this->parent) ? $this->parent->debug() : null,
        ];
    }
}