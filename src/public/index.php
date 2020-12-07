<?php
define('BASE',dirname(__DIR__));
define('START_TIME',microtime());
require dirname(__DIR__) . '/vendor/autoload.php';

use Main\Bootstrap\Config;

Config::env();

if (ENV_DEV) {
    error_reporting(E_ALL);
} else {
    ini_set( 'display_errors' , 0);
    error_reporting(E_ERROR);
}

$container = require BASE . DIRECTORY_SEPARATOR . 'Main' .DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'DI' . DIRECTORY_SEPARATOR . 'bootstrap.php';

require_once BASE. DIRECTORY_SEPARATOR . 'App' .DIRECTORY_SEPARATOR . 'Route' . DIRECTORY_SEPARATOR . 'routers.php';