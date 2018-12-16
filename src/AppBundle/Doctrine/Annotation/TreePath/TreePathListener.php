<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 11:58 AM
 */

namespace AppBundle\Doctrine\Annotation\TreePath;


use AppBundle\Doctrine\Annotation\MappedEventListener;
use AppBundle\Entity\Area;
use Doctrine\Common\PropertyChangedListener;

class TreePathListener extends MappedEventListener implements PropertyChangedListener
{
    /**
     * @param Area $subject
     * @return bool
     */
    public function isCodeChange(Area $subject): bool
    {
        return $this->isUpdatedFields($subject, ['code']);
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function postLoad(Area $subject)
    {
        $subject->addPropertyChangedListener($this);
    }

    public function propertyChanged($sender, $propertyName, $oldValue, $newValue)
    {
        if ('code' === $propertyName) {
            exit('a');
        }
    }

    public function preFlush(Area $subject)
    {
        if ($this->isUpdatedFields($subject, ['path'])) {
            return;
        }

        if ($this->isNew($subject) || $this->isCodeChange($subject)) {
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
            $child->setPath($this->generatePath($child));
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