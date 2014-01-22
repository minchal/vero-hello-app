<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use App\Entity\History\LoggableListener;
use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Vendor\Doctrine\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Common\EventManager;
use Gedmo\Tree\TreeListener;

class Doctrine extends LazyService
{
    protected function create(Container $c)
    {
        $conf = $c -> get('config');

        $config = Setup::createConfiguration($conf -> get('debug'));
        $config -> setProxyDir($c -> get('app') -> path('var/'));
        $config -> addCustomStringFunction('MATCH', 'Vero\Vendor\Doctrine\Query\Mysql\MatchAgainst');
        $config -> addCustomNumericFunction('RAND', 'Vero\Vendor\Doctrine\Query\Mysql\Rand');

        $driver = new SimplifiedXmlDriver([
            $c -> get('app') -> path('resources/doctrine/') => 'App\Entity',
        ]);

        $config -> setMetadataDriverImpl($driver);
        $config -> setDefaultRepositoryClassName('\Vero\Vendor\Doctrine\EntityRepository');

        if ($conf -> get('debug')) {
            $config -> setSQLLogger(new DebugStack());
        }

        $evm = new EventManager();
        $evm -> addEventSubscriber(new TreeListener());
        $evm -> addEventSubscriber(new LoggableListener($c));

        $em = EntityManager::create($conf -> get('database'), $config, $evm);
        $em -> getConnection() -> getDatabasePlatform() -> registerDoctrineTypeMapping('enum', 'string');

        return $em;
    }
}
