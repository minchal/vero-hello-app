<?php

use Vero\DependencyInjection\Services as VeroServices;
use Vero\DependencyInjection\CallbackFactory;
use App\Services\Core;

return [
    'log' => new VeroServices\Logger(),
    'cache' => new VeroServices\Cache(),
    'acl' => new VeroServices\ACL(),
    'acl-backend' => new Core\ACLBackend(),
    'event-dispatcher' => new Core\EventDispatcher(),
    'i18n' => new Core\I18n(),
    'router' => new VeroServices\Router(),
    'session' => new VeroServices\Session(),
    'doctrine' => new Core\Doctrine(),
    'mongo' => new Core\Mongo(),
    'settings' => new Core\Settings(),
    'auth' => new VeroServices\Auth(),
    'auth-provider' => new Core\AuthProvider(),
    'autologin-provider' => new Core\AutologinProvider(),
    'mailer' => new Core\Mailer(),
    'repository' => new Core\RepositoryFactory(),
    'vfc' => new Core\VFCFactory(),
    'twig' => new Core\TwigFactory(),
    'templating' => new Core\TemplatingFactory(),
    'mail' => new Core\MailTemplateFactory(),
    'imagine' => new CallbackFactory(function () {
        return new \Imagine\Gd\Imagine();
    }),
];
