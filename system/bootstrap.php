<?php
/**
 * Bootstrap application.
 * This file can be freely changed, if app needs that.
 * 
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

require __DIR__.'/app/autoload.php';

return new \App\App(new \Vero\Config\FileArray(__DIR__.'/config.php'));
