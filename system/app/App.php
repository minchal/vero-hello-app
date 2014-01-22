<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App;

use Vero\Debug;
use Vero\DependencyInjection\MapLoader;
use Vero\Web\Request;

/**
 * Application specific for this project.
 */
class App extends \Vero\Application\App
{
    const VERSION = '1.4';

    protected function prepareEnvironment()
    {
        parent::prepareEnvironment();

        $config = $this -> config;

        (new Debug\ExceptionHandler($config, __DIR__ . '/critical.phtml'))
            -> addLogListener($this -> container)
            -> addListener(function (&$data) use ($config) {
                $data['crypted'] = Debug\CryptEnv::encrypt($data, $config -> get('keys.debug'));
            })
            -> register();

        (new Debug\ErrorHandler())
            -> register();

        $this
            -> registerPath('base', __DIR__ . '/../../')
            -> registerPath('app', __DIR__)
            -> registerPath('lib', __DIR__ . '/../lib/')
            -> registerPath('vendor', __DIR__ . '/../vendor/')
            -> registerPath('var', __DIR__ . '/../var/')
            -> registerPath('upload', __DIR__ . '/../../upload/')
            -> registerPath('resources', __DIR__ . '/resources/')
        ;
    }

    protected function prepareContainer()
    {
        parent::prepareContainer();
        
        $loader = new MapLoader($this -> container);
        $loader -> loadAll($this -> path('resources/services/'));
    }

    public function run($controller = '\Vero\Web\Controller')
    {
        $controller = new $controller($this -> container);

        if ($controller instanceof \Vero\Web\Controller) {
            $controller -> setExceptionHandler('App\Action\Error');
            $this -> container -> set('request', function () {
                return Request::createFromGlobals();
            });
        }

        return $controller -> run();
    }

    public function signature()
    {
        if ($this -> debug) {
            $queries = $this -> container -> get('doctrine') -> getConnection()
                -> getConfiguration() -> getSQLLogger() -> queries;
            
            return sprintf(
                'Version: %s (%s); Time: %01.4fs; SQL: %d; Files: %d; Memory: %01.2fMB %s',
                self::VERSION,
                \Vero\Version::VERSION,
                microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
                count($queries),
                count(get_included_files()),
                memory_get_usage(true)/1024/1024,
                isset($_GET['sql']) ? print_r($queries, true) : ''
            );
        }
        
        return sprintf(
            'Version: %s; Time: %01.4fs',
            self::VERSION,
            microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
        );
    }
}
