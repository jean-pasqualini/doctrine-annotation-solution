<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 1/13/19
 * Time: 11:16 AM
 */

namespace AppBundle\Doctrine\Annotation\OrderedProcessor;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class OrderedProcessor extends Annotation
{
    public $sort = [];

    public static function getNamespace()
    {
        return __NAMESPACE__;
    }

    public static function getName()
    {
        return 'ordered_procesor';
    }
}