<?php
/**
 * Prepare autoloader for application.
 * 
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

$libs = realpath(__DIR__.'/../vendor');
require $libs.'/minchal/vero/src/Vero/Loader/UniversalLoader.php';

return (new \Vero\Loader\UniversalLoader())
    -> addDirect('App', __DIR__)
    -> addAll(require $libs.'/composer/autoload_namespaces.php')
    -> add('Swift', $libs.'/swiftmailer/swiftmailer/lib/classes/')

/*
$libs = realpath(__DIR__.'/../lib');
require $libs.'/Vero/Loader/UniversalLoader.php';

return (new \Vero\Loader\UniversalLoader())
    -> addDirect('App', __DIR__)
    -> add('', $libs)
    -> add('Swift', $libs.'/swiftmailer/classes/')
*/
    
    -> register()
;
