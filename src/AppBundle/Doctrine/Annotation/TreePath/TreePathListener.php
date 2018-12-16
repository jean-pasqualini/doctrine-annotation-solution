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
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TreePathListener
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function isNew(Area $area)
    {
        return empty($this->em->getUnitOfWork()->getOriginalEntityData($area));
    }

    protected function isUpdatedFields($subject, array $fields)
    {
        $changeSet = $this->em->getUnitOfWork()->getEntityChangeSet($subject);
        foreach ($fields as $field) {
            if (isset($changeSet[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function isUpdated($subject)
    {
        return !empty($this->em->getUnitOfWork()->getEntityChangeSet($subject));
    }

    public function preFlush(Area $subject)
    {
        if ($this->isUpdatedFields($subject, ['path'])) {
            return;
        }

        if ($this->isUpdatedFields($subject,['code'])) {
            $this->updatePath($subject);
            $this->updateChildrenPath($subject);
        }
    }

    private function updatePath(Area $subject)
    {
        $subject->path = $this->generatePath($subject);
    }

    private function updateChildrenPath(Area $subject)
    {
        /** @var Area[] $children */
        $children = $subject->getChildren();

        foreach ($children as $child) {
            $child->path = $this->generatePath($child);
        }
    }

    private function generatePath(Area $area)
    {
        if (null === $area->getParent()) {
            return $area->code;
        }

        return $this->generatePath($area->getParent()).'-'.$area->code;
    }
}