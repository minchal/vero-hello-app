<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\DependencyInjection\Dependent;
use Vero\Helper\InstancesConstructor as IC;
use Symfony\Component\EventDispatcher\EventDispatcher as Dispatcher;

class EventDispatcher extends LazyService
{
    protected function create(Container $c)
    {
        $dispatcher = new Dispatcher();
        
        // Allow to register for Packages
        $packages = IC::create($c -> get('app') -> path('app/Package/'), 'App\Package\\');
        
        foreach ($packages as $pkg) {
            if ($pkg instanceof Dependent) {
                $pkg -> setContainer($c);
            }
            
            $dispatcher -> addSubscriber($pkg);
        }
        
        return $dispatcher;
    }
}
