<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 11:50 PM
 */

namespace AppBundle\Doctrine\Annotation\TreePath;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class TreePath extends Annotation
{
    public $comment;

    public $separator;

    public $source;
}