<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;

class AuthProvider extends LazyService
{
    protected function create(Container $c)
    {
        $provider = $c -> get('doctrine') -> getRepository('App\Entity\User\User');
        $provider -> setGlobalSalt($c -> get('config') -> get('keys.salt'));
        return $provider;
    }
}
