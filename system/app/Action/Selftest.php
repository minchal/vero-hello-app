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

        if (class_exists('App\Entity\User')) {
            $user = $this -> get('auth') -> getUser();
        }

        if (!$app -> debug() && (!isset($user) || $this -> user -> getId() != 1)) {
            throw $this -> notFound();
        }

        if ($req -> get('phpinfo') !== null) {
            phpinfo();
            return;
        }

        $info = [];

        $info[] = 'General Information';
        $info[] = ['Debug Mode', null, $app -> debug()];
        $info[] = ['CMS Version', \App\App::VERSION];
        $info[] = ['Vero Framework Version', \Vero\Version::VERSION];
        $info[] = ['Doctrine Version', \Doctrine\ORM\Version::VERSION];
        $info[] = ['Twig Version', \Twig_Environment::VERSION];

        $info[] = 'Environment';
        $info[] = ['PHP version', PHP_VERSION];
        $info[] = ['OS', php_uname()];
        $info[] = ['include_path', ini_get('include_path')];
        $info[] = ['iconv extension', null, extension_loaded('iconv')];
        $info[] = ['cURL extension', null, extension_loaded('curl')];
        $info[] = ['Openssl extension', null, extension_loaded('openssl')];

        $info[] = 'Database';

        try {
            $db = $this -> em() -> getConnection();
            $info[] = [
                'Connection',
                get_class($db -> getDriver()),
                $db -> executeQuery('SELECT 2+2') -> fetchAll()
            ];
        } catch (\Exception $e) {
            $info[] = ['Connection', $e -> getMessage(), false];
        }

        $info[] = 'Session';
        $info[] = ['Session ID', $this -> session -> getId()];
        $info[] = ['Last Query', $this -> session -> lastQuery];
        $info[] = ['Last Time', date('Y-m-d H:i:s', $this -> session -> lastTime)];

        if (isset($user)) {
            $info[] = ['Authorized user', $user -> getLogin(), !$user -> isGuest()];
            $info[] = ['Autologin', null, $this -> get('auth') -> usesAutologin()];
        }

        return new Simple(
            $app -> path('resources/views/selftest.php'), [
            'title' => $config -> get('app.project', 'VeroCMS') . ' - SelfTest',
            'signature' => $app -> signature(),
            'info' => $info
            ]
        );
    }
}
