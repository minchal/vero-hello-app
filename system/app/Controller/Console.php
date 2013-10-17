<?php

namespace App\Controller;

use Vero\Application\Controller;
use Vero\Tools\Console\ConsoleRunner;
use Vero\Tools\Console\Helper\DIContainerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
/**
 * Application console.
 * Currently only Doctrine console.
 */
class Console extends Controller
{
    public function run()
    {
        $em = $this -> container -> get('doctrine');

        $app = new Application('Vero CLI', \Vero\Version::VERSION);
        $app -> setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($em -> getConnection()),
            'em' => new EntityManagerHelper($em),
            'di' => new DIContainerHelper($this -> container),
        ]));

        ConsoleRunner::addCommands($app);
        DoctrineConsoleRunner::addCommands($app);

        $app -> run();
    }
}
