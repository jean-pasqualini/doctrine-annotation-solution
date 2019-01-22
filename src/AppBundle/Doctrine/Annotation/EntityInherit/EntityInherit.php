<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/15/18
 * Time: 4:34 PM
 */

namespace AppBundle\Doctrine\Annotation\EntityInherit;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotationx
 * @Target("PROPERTY")
 */
class EntityInherit extends Annotation
{
    public $comment = '';
    public $tech_key;

    public static function getNamespace()
    {
        return __NAMESPACE__;
    }
}