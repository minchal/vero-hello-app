<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Config\FileJson;

class Settings extends LazyService
{
    protected function create(Container $c)
    {
        $config = new FileJson(
            $c -> get('app') -> path('resources/settings.json'),
            true
        );

        $c -> get('controller') -> addSendListener(array($config, 'saveChanged'));

        return $config;
    }
}
