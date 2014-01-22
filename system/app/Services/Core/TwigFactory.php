<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Factory;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extension_Debug;
use Vero\Vendor\Twig as T;

class TwigFactory extends Factory
{
    public function get()
    {
        $c = $this -> container;

        $twig = new Twig_Environment(
            new Twig_Loader_Filesystem($c -> get('app') -> path('resources/views/')),
            [
                'cache' => $c -> get('app') -> path('var/twig'),
                'debug' => $c -> get('config') -> get('debug'),
            ]
        );

        if ($c -> get('config') -> get('debug')) {
            $twig -> addExtension(new Twig_Extension_Debug());
        }

        $twig -> addExtension(new T\JqueryValidateExtension());
        $twig -> addExtension(new T\I18nExtension($c -> get('i18n')));
        $twig -> addExtension(new T\ACLExtension($c));
        $twig -> addExtension(new T\RouterExtension(
            $c -> get('router'),
            $c -> get('router') -> url() -> setPrefix('') -> setAction('public/')
        ));

        $twig -> addGlobal('config', $c -> get('config') -> get('app'));
        $twig -> addGlobal('settings', $c -> get('settings'));

        if ($c -> has('request')) {
            $twig -> addGlobal('request', $c -> get('request'));
        }

        return $twig;
    }
}
