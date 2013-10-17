<?php
/**
 * Services for Dependency Injection.
 * 
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services;

use Vero;
use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\DependencyInjection\Factory;

class Doctrine extends LazyService
{
    protected function create(Container $c)
    {
        $conf = $c -> get('config');
        
        $config = \Doctrine\ORM\Tools\Setup::createConfiguration(
            $conf -> get('debug')
        );
        
        $driver = new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver([
            $c->get('app')->path('resources/doctrine/') => 'App\Entity'
        ]);
        
        $config -> setMetadataDriverImpl($driver);
        $config -> setDefaultRepositoryClassName('\Vero\Vendor\Doctrine\EntityRepository');
        
        if ($conf -> get('debug')) {
            $config -> setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());
        }
        
        $evm = new \Doctrine\Common\EventManager();
        $evm -> addEventSubscriber(new \Gedmo\Tree\TreeListener());
        
        $em = Vero\Vendor\Doctrine\EntityManager::create($conf -> get('database'), $config, $evm);
        
        $connection = $em -> getConnection();
        $connection -> getDatabasePlatform() -> registerDoctrineTypeMapping('enum', 'string');
        
        return $em;
    }
}

class Cache extends LazyService
{
    protected function create(Container $c)
    {
        return new Vero\Cache\Cache(
            new \Doctrine\Common\Cache\FilesystemCache(
                $c -> get('app') -> path('var/cache/')
            )
        );
    }
}

class ACL extends LazyService
{
    protected function create(Container $c)
    {
        $acl = new Vero\ACL\ACL(
            new Vero\ACL\Backend\XML(
                $c -> get('app') -> path('resources/acl/'),
                $c -> get('cache')
            )
        );
        
        if ($c -> has('auth')) {
            $acl -> setSessionRole($c -> get('auth') -> getUser());
        }
        
        return $acl;
    }
}

class I18n extends LazyService
{
    protected function create(Container $c)
    {
        return new Vero\I18n\Translator(
            new Vero\I18n\Backend\Xliff(
                $c -> get('cache'),
                $c -> get('app') -> path('resources/i18n/')
            ),
            $c -> get('config') -> get('language','en'),
            array(
                'pl'=>'pl_PL',
                'en'=>'en_US',
            )
        );
    }
}

class Log extends LazyService
{
    protected function create(Container $c)
    {
        return new Vero\Log\Logger(
            $c -> get('config') -> get('log.level', 'info'),
            new Vero\Log\Backend\File(
                $c -> get('app') -> path('var/log/')
            )
        );
    }
}

class Router extends LazyService
{
    protected function create(Container $c)
    {
        $config = $c -> get('config');
        
        $base   = $config -> get('routing.base', '/');
        $prefix = $config -> get('routing.prefix');
        
        if ($c -> has('request')) {
            $req = $c -> get('request');
            $domain = $config -> get('routing.domain', $req -> host());
            $scheme = $config -> get('routing.scheme', ($req -> isSecure() ? 'https' : 'http'));
        } else {
            $domain = $config -> get('routing.domain');
            $scheme = $config -> get('routing.scheme');
        }
        
        $router = new Vero\Routing\Router($base, $prefix, $domain, $scheme);
        
        $builder = new Vero\Routing\Builder\XML(
            $c -> get('app') -> path('resources/routes/'), 
            $c -> get('cache')
        );
        
        return $builder -> fill($router);
    }
}

class Settings extends LazyService
{
    protected function create(Container $c)
    {
        $config = new Vero\Config\FileJson(
            $c -> get('app') -> path('resources/settings.json'), true
        );
        
        $c -> get('controller') -> addSendListener(array($config, 'saveChanged'));
        
        return $config;
    }
}

/**
 * Services to run only in HTTP request.
 */

class Request extends LazyService
{
    protected function create(Container $c)
    {
        return new Vero\Web\Request(
            $c -> get('config') -> get('routing.base', '/'),
            $c -> get('config') -> get('routing.prefix')
        );
    }
}

class Session extends LazyService
{
    protected function create(Container $c)
    {
        $session = new Vero\Web\Session\Session(
            new Vero\Web\Session\Backend\File($c -> get('app') -> path('var/session/')),
            /*new Vero\Web\Session\Backend\Database(
                $c -> get('doctrine') -> getConnection(), 
                $c -> get('config') -> get('database.prefix').'session'
            ),*/
            $c -> get('config') -> get('session',[])
        );
        
        $c -> get('controller') -> addSendListener(array($session, 'close'));
        
        $session -> start($c -> get('request'));
        
        return $session;
    }
}

class AuthProvider extends LazyService
{
    protected function create(Container $c)
    {
        $provider = $c -> get('doctrine') -> getRepository('App\Entity\User');
        $provider -> setGlobalSalt($c -> get('config') -> get('keys.salt'));
        return $provider;
    }
}

class Auth extends LazyService
{
    protected function create(Container $c)
    {
        $auth = new Vero\Web\Auth\Manager(
            $c -> get('session'), $c -> get('auth-provider')
        );
        
        $request = $c -> get('request');
        
        $c -> get('controller') -> addSendListener(function($response) use ($request, $auth) {
            $auth -> visit($request);
        });
        
        if ($auth -> usesAutologin()) {
            $auth -> addLoadListener(function($user) use($request, $auth) {
                if ($user -> isGuest()) {
                    $auth -> autologin($request);
                }
            });
        }
        
        return $auth;
    }
}

class Mailer extends LazyService
{
    protected function create(Container $c)
    {
        require $c -> get('app') -> path('lib/swiftmailer/swift_init.php');
        
        return Vero\Vendor\Swift\MailerFactory::create($c -> get('config'));
    }
}

/**
 * Factories.
 */

class Twig extends Factory
{
    public function get()
    {
        $c = $this -> container;
        
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem($c -> get('app') -> path('resources/views/')),
            [
                'cache' => $c -> get('app') -> path('var/twig'),
                'debug' => $c -> get('config') -> get('debug'),
                //'strict_variables' => true,
            ]
        );
        
        if ($c -> get('config') -> get('debug')) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }
        
        $twig -> addExtension(new Vero\Vendor\Twig\JqueryValidateExtension());
        $twig -> addExtension(new Vero\Vendor\Twig\I18nExtension($c -> get('i18n')));
        $twig -> addExtension(new Vero\Vendor\Twig\ACLExtension($c));
        $twig -> addExtension(new Vero\Vendor\Twig\RouterExtension(
            $c -> get('router'),
            $c -> get('router') -> url() -> setAction('public/')
        ));
        
        $twig -> addGlobal('config', $c -> get('config') -> get('app'));
        $twig -> addGlobal('settings', $c -> get('settings'));
        
        if ($c -> has('request')) {
            $twig -> addGlobal('request', $c -> get('request'));
        }
        
        return $twig;
    }
}

class Templating extends Factory
{
    public function get($tpl = null, array $data = [])
    {
        // if no template name specified, 
        // try to guess file name from router RouteID
        if (is_array($tpl) && !$data || !$tpl) {
            $data = (array) $tpl;
            $tpl = $this -> container -> get('request') -> action . '.twig';
        }
        
        $view = new Vero\View\Twig($tpl, $data);
        $view -> setContainer($this -> container);
        return $view;
    }
}

class MailTemplate extends Factory
{
    public function get($tpl = null)
    {
        $c = $this -> container;
        $conf = $c -> get('config');
        
        $twig = $c -> get('twig');
        $twig -> setLoader(new \Twig_Loader_String());
        
        $mailer = $c -> get('mailer');
        
        $mail = new Vero\Vendor\Swift\JsonTemplate($mailer, $twig);
        
        $mail -> setFrom($conf -> get('mail.from', 'no-reply@localhost'));
        
        if ($tpl) {
            $mail -> setTemplate(
                $c -> get('app') -> path('resources/mail/'.$tpl.'.json')
            );
        }
        
        return $mail;
    }
}

class Repository extends Factory
{
    public function get($repo = null, $ns = 'App\Entity')
    {
        if (!$repo) {
            list($repo) = explode('/', $this -> container -> get('request') -> action);
            $repo = ucfirst($repo);
        }
        
        return $this -> container -> get('doctrine') -> getRepository($ns.'\\'.$repo);
    }
}

class VFC extends Factory
{
    public function get($name = null, $args = [])
    {
        if (is_array($name)) {
            $args = $name;
            $name = null;
        }
        
        if (!$name) {
            list($name) = explode('/', $this -> container -> get('request') -> action);
            $name = ucfirst($name);
        }
        
        $refl = new \ReflectionClass('\App\VFC\\'.$name);
        return $refl -> newInstanceArgs($args);
    }
}

class Imagine extends Factory
{
    public function get()
    {
        return new \Imagine\Gd\Imagine();
    }
}
