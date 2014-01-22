<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Vendor\Swift\MailerFactory;

class Mailer extends LazyService
{
    protected function create(Container $c)
    {
        require $c -> get('app') -> path('vendor/swiftmailer/swiftmailer/lib/swift_init.php');

        return MailerFactory::create($c -> get('config'));
    }
}
