<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/16/18
 * Time: 11:54 PM
 */

namespace AppBundle\Doctrine\Annotation\StringGenerator;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class StringGenerator extends Annotation
{
    public $comment;

    public $value;

    public $vars;
}