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
        if (!function_exists('password_hash')) {
            require $c -> get('app') -> path('vendor/ircmaxell/password-compat/lib/password.php');
        }
        
        return $c -> get('doctrine') -> getRepository('App\Entity\User\User');
    }
}
