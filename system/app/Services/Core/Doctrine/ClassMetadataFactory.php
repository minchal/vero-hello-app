<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

class ClassMetadataFactory extends BaseClassMetadataFactory
{
    protected function getShortName($className)
    {
        $parts = explode('\\', strtolower($className));
        
        $last = array_pop($parts);
        
        if (!$parts) {
            return $last;
        }
        
        return array_pop($parts).'_'.$last;
    }
}
