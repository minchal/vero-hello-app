<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Web\Auth\Autologin\DatabaseProvider;

class AutologinProvider extends LazyService
{
    protected function create(Container $c)
    {
        return new DatabaseProvider(
            $c -> get('doctrine') -> getConnection(),
            $c -> get('config') -> get('database.prefix').'autologin'
        );
    }
}
