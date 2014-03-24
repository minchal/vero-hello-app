<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\ACL\Backend;

class ACLBackend extends LazyService
{
    protected function create(Container $c)
    {
        $provider = $c -> get('user.role-provider');
        
        if ($provider instanceof Backend) {
            return $provider;
        }
        
        return new Backend\Json(
            $c -> get('app') -> path('resources/acl/'),
            $c -> get('cache')
        );
    }
}
