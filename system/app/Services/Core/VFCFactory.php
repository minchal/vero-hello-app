<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Factory;

class VFCFactory extends Factory
{
    public function get($name = null, $args = [])
    {
        if (is_array($name)) {
            $args = $name;
            $name = null;
        }

        if (!$name) {
            list($name) = explode('/', $this -> container -> get('request') -> action);
            $name = ucfirst($name);
        }

        $refl = new \ReflectionClass('\App\VFC\\' . $name);
        return $refl -> newInstanceArgs($args);
    }
}
