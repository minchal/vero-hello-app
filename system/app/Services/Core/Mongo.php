<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use MongoClient;

class Mongo extends LazyService
{
    protected function create(Container $c)
    {
        $config = $c -> get('config');
        
        $client = new MongoClient(
            $config -> get('mongo.server'),
            $config -> get('mongo.options', [])
        );
        
        return $client -> selectDB($config -> get('mongo.name', 'vero'));
    }
}
