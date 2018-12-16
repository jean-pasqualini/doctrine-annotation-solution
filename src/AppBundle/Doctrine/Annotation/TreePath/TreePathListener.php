<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 11:58 AM
 */

namespace AppBundle\Doctrine\Annotation\TreePath;


use AppBundle\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;

class TreePathListener
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function preUpdate(Area $subject)
    {
        $subject->path = $this->generatePath($subject);

        /** @var Area[] $children */
        $children = $subject->getChildren();

        foreach ($children as $child) {
            $child->path = $this->generatePath($child);

            $this->em->getUnitOfWork()->recomputeSingleEntityChangeSet(
                $this->em->getClassMetadata(get_class($subject)),
                $subject
            );
        }
    }

    public function prePersist(Area $subject)
    {
        $subject->path = $this->generatePath($subject);
    }

    private function generatePath(Area $area)
    {
        if (null === $area->getParent()) {
            return $area->code;
        }

        return $this->generatePath($area->getParent()).'-'.$area->code;
    }
}