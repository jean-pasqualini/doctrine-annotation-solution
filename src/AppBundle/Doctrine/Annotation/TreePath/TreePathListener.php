<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 11:58 AM
 */

namespace AppBundle\Doctrine\Annotation\TreePath;


use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Doctrine\DomainObject;
use AppBundle\Entity\Area;
use Doctrine\Common\PropertyChangedListener;

class TreePathListener
{
    /**
     * @param Area $subject
     * @return bool
     */
    protected function isCodeChange(DomainObject $subject): bool
    {
        return $subject->isPropertyChanged('code');
    }

    /**
     * @param Area $subject
     * @return bool
     */
    protected function isAlreadyPathUpdated(DomainObject $subject): bool
    {
        return $subject->isPropertyChanged('path');
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function preFlush(Area $subject)
    {
        if ($this->isAlreadyPathUpdated($subject)) {
            return;
        }

        if ($this->isCodeChange($subject)) {
            $this->updatePath($subject);
            $this->updateChildrenPath($subject);
        }

    }

    private function updatePath(Area $subject)
    {
        $subject->setPath($this->generatePath($subject));
    }

    private function updateChildrenPath(Area $subject)
    {
        /** @var Area[] $children */
        $children = $subject->getChildren();

        foreach ($children as $child) {
            if ($this->isCodeChange($child)) {
                $child->setPath($this->generatePath($child));
            }
        }
    }

    private function generatePath(Area $area)
    {
        if (null === $area->getParent()) {
            return $area->getCode();
        }

        return $this->generatePath($area->getParent()).'-'.$area->getCode();
    }
}