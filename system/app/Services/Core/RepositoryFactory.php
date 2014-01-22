<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Factory;

class RepositoryFactory extends Factory
{
    public function get($repo = null, $ns = 'App\Entity')
    {
        if (!$repo) {
            list($repo) = explode('/', $this -> container -> get('request') -> action);
            $repo = ucfirst($repo);
        }

        return $this -> container -> get('doctrine') -> getRepository($ns . '\\' . $repo);
    }
}
