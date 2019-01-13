<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/15/18
 * Time: 9:50 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class SequencedCode extends Annotation
{
    public $tree = false;
    public $comment = '';
    public $tech_key;
}