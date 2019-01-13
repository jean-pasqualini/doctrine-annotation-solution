<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 10:13 PM
 */

namespace AppBundle\Entity;

use AppBundle\Doctrine\Annotation\EntityInherit\EntityInherit;
use AppBundle\Doctrine\Annotation\OrderedProcessor\OrderedProcessor;
use AppBundle\Doctrine\Annotation\SequencedCode\SequencedCode;
use AppBundle\Doctrine\Annotation\TreePath\TreePath;
use AppBundle\Doctrine\DomainObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Doctrine\Annotation\StringGenerator\StringGenerator;

/**
 * @ORM\Entity()
 * @OrderedProcessor(sort={"entity_inherit", "generate_code"})
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
     * @var Area
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="children", cascade={"persist"})
     */
    protected $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Area", mappedBy="parent", cascade={"persist"})
     */
    protected $children;

    /**
     * @SequencedCode(
     *     tree=true,
     *     tech_key="generate_code",
     *     comment="on génère une séquence de type AA qui définit le code de mon area lui même propre à chaque noeud parent/entity"
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @EntityInherit(tech_key="entity_inherit", comment="on hérite de la boutique du parent afin de rester cohérent")
     */
    public $entity;

    /**
     * @StringGenerator(
     *     value="area %s",
     *     vars="code",
     *     comment="on génère le label de l'emplacement de manière auto au format area {code}"
     * )
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    public $title;

    /**
     * @TreePath(separator="-", source="code", comment="on génère un fil d'ariane de type A1 - AA à partir du champ code")
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

    public function setParent(Area $parent)
    {
        if ($this->parent === $parent) {
            return;
        }
        if ($this->parent) {
            $this->parent->removeChildren($this);
        }

        $this->propertyChanged('parent');

        $this->parent = $parent;
        $parent->addChildren($this);
    }

    public function addChildren(Area $child)
    {
        if ($this->children->contains($child)) {
            return;
        }
        $this->children->add($child);
        $child->setParent($this);
    }

    public function removeChildren(Area $child)
    {
        $this->children->removeElement($child);
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity)
    {
        $this->propertyChanged('entity');
        $this->entity = $entity;
    }

    public function setCode($code)
    {
        $this->propertyChanged('code');
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->propertyChanged('path');
        $this->path = $path;
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

    public function getClass()
    {
        return __CLASS__;
    }
}