<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 12/14/18
 * Time: 11:48 PM
 */

namespace AppBundle\Doctrine\Annotation\SequencedCode\Mapping\Driver;


use Gedmo\Mapping\Driver\AbstractAnnotationDriver;

class Annotation extends AbstractAnnotationDriver
{
    public function readExtendedMetadata($meta, array &$config)
    {
        $config['sequenced_code'] = [
            'field' => 'code'
        ];
    }
}