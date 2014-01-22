<?php
/**
 * Start application as standard routed request.
 * 
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

// comment in production
// @see http://symfony.com/doc/current/book/installation.html#configuration-and-setup
umask(0);

// restrict access to application
if (file_exists('_dev.php')) {
	require '_dev.php';
}

$app = require __DIR__.'/system/bootstrap.php';
$app -> run();

//var_dump(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']);
//var_dump(get_included_files());
