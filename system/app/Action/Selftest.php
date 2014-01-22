<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;
use Vero\View\Simple;

class Selftest extends Web\Action\Session
{
    public function run(Web\Request $req)
    {
        $app = $this -> get('app');
        $config = $this -> get('config');

        if (class_exists('App\Entity\User\User')) {
            $user = $this -> get('auth') -> getUser();
        }

        if (!$app -> debug() && (!isset($user) || $this -> user -> getId() != 1)) {
            throw $this -> notFound();
        }

        if ($req -> get('phpinfo') !== null) {
            phpinfo();
            return;
        }
        
        $general[] = ['Debug Mode', null, $app -> debug()];
        $general[] = ['CMS Version', \App\App::VERSION];
        $general[] = ['Vero Framework Version', \Vero\Version::VERSION];
        $general[] = ['Doctrine Version', \Doctrine\ORM\Version::VERSION];
        $general[] = ['Twig Version', \Twig_Environment::VERSION];

        $env[] = ['PHP version', PHP_VERSION];
        $env[] = ['OS', php_uname()];
        $env[] = ['include_path', ini_get('include_path')];
        $env[] = ['iconv extension', null, extension_loaded('iconv')];
        $env[] = ['cURL extension', null, extension_loaded('curl')];
        $env[] = ['Openssl extension', null, extension_loaded('openssl')];
        $env[] = ['MongoDB', null, extension_loaded('mongo')];

        try {
            $conn = $this -> em() -> getConnection();
            $db[] = [
                'Connection',
                get_class($conn -> getDriver()),
                $conn -> executeQuery('SELECT 2+2') -> fetchAll()
            ];
        } catch (\Exception $e) {
            $db[] = ['Connection', $e -> getMessage(), false];
        }

        $session[] = ['Session ID', $this -> session -> getId()];
        $session[] = ['Last Query', $this -> session -> lastQuery];
        $session[] = ['Last Time', date('Y-m-d H:i:s', $this -> session -> lastTime)];

        if (isset($user)) {
            $session[] = ['Authorized user', $user -> getLogin(), !$user -> isGuest()];
            $session[] = ['Autologin', null, $this -> get('auth') -> usesAutologin()];
        }
        
        $tests['General Information'] = $general;
        $tests['Environment'] = $env;
        $tests['Database'] = $db;
        $tests['Session'] = $session;
        
        $loader = null;
        
        foreach (spl_autoload_functions() as $al) {
            if (isset($al[0]) && $al[0] instanceof \Vero\Loader\UniversalLoader) {
                $loader = $al[0];
                break;
            }
        }
        
        return new Simple(
            $app -> path('resources/views/selftest.php'),
            [
                'title' => $config -> get('app.project', 'VeroCMS') . ' - SelfTest',
                'signature' => $app -> signature(),
                'locales' => $this -> i18n -> getAcceptedLocales(),
                'routes' => $this -> router -> getRoutes(),
                'tests' => $tests,
                'autoload' => $loader ? $loader -> getNamespaces() : [],
            ]
        );
    }
}
