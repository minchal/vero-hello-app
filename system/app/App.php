<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App;
use App\Services;
use Vero\Debug;

/**
 * Application specific for this project.
 */
class App extends \Vero\Application\App
{
    const VERSION = '1.0';
    
    protected function prepareEnvironment()
    {
        parent::prepareEnvironment();
        
        $config = $this -> config;
        
        (new Debug\ExceptionHandler($this -> config, __DIR__.'/critical.phtml'))
            -> addLogListener($this -> container)
            -> addListener(function(&$data) use($config) {
                $data['crypted'] = Debug\CryptEnv::encrypt($data, $config->get('keys.debug'));
            })
            -> register();
        
        (new Debug\ErrorHandler())
            -> register();
        
        $this
             -> registerPath('base',      __DIR__.'/../../')
             -> registerPath('app',       __DIR__)
             -> registerPath('lib',       __DIR__.'/../lib/')
             -> registerPath('var',       __DIR__.'/../var/')
             -> registerPath('upload',    __DIR__.'/../../upload/')
             -> registerPath('resources', __DIR__.'/resources/')
        ;
    }
    
    protected function prepareContainer()
    {
        parent::prepareContainer();
        
        $this -> container
            -> set('log',       new Services\Log())
            -> set('cache',     new Services\Cache())
            -> set('acl',       new Services\ACL())
            -> set('i18n',      new Services\I18n())
            -> set('doctrine',  new Services\Doctrine())
            -> set('repository',new Services\Repository())
            -> set('vfc',       new Services\VFC())
            -> set('settings',  new Services\Settings())
            -> set('templating',new Services\Templating())
            -> set('twig',      new Services\Twig())
            -> set('mailer',    new Services\Mailer())
            -> set('mail',      new Services\MailTemplate())
            -> set('imagine',   new Services\Imagine())
        ;
    }
    
    public function run($controller = '\Vero\Web\Controller')
    {
        $controller = new $controller($this -> container);
        
        if ($controller instanceof \Vero\Web\Controller) {
            $this -> container
                -> set('request',       new Services\Request())
                -> set('router',        new Services\Router())
                -> set('session',       new Services\Session())
                -> set('auth-provider', new Services\AuthProvider())
                -> set('auth',          new Services\Auth())
            ;
            
            $controller -> setExceptionHandler('App\Action\Error');
        }
        
        return $controller -> run();
    }
    
    public function signature()
    {
        if ($this -> debug) {
            $queries = $this -> container -> get('doctrine') -> getConnection() -> getConfiguration() -> getSQLLogger() -> queries;
            
            return sprintf(
                'Version: %s (%s); Time: %01.4fs; SQL: %d; Files: %d; Memory: %01.2fMB %s', 
                self::VERSION, \Vero\Version::VERSION, 
                microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
                count($queries), 
                count(get_included_files()), 
                memory_get_usage(true)/1024/1024,
                isset($_GET['sql']) ? print_r($queries, true) : ''
            );
        }
        
        return sprintf(
            'Version: %s; Time: %01.4fs; SQL: %d', 
            self::VERSION, 
            microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            count($queries)
        );
    }
}
