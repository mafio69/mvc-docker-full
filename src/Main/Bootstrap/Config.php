<?php
namespace Main\Bootstrap;


use Main\Tools\XmlParse;

class Config
{
    public static function env()
    {


        $env = new XmlParse();
        $envArray = $env->readENV(BASE . '/ENV.xml');
        define('ENV_DEV', $envArray['developer'] == 'true');
        $email = explode(',', trim($envArray['emailLog']));
        define('EMAIL_LOG', $email);
        define('SITE_NAME', $envArray['siteName']);
        define('DB_HOST', $envArray['dbHost']);
        define('DB_NAME', $envArray['dbName']);
        define('DB_USER', $envArray['dbUser']);
        define('DB_PASSWORD', $envArray['dbPassword'] == 'empty' ? '' : $envArray['dbPassword']);
        define('SM_HOST', $envArray['host']);
        define('SM_PORT', $envArray['port']);
        define('SM_ENCY', $envArray['encryption']);
        define('SM_USER', $envArray['user']);
        define('SM_PASSWORD', $envArray['password']);
        define('URI', isset($_SERVER['REQUEST_URI']) ?? '/test');
        define('HOST', isset($_SERVER['HTTP_HOST']) ?? 'www.test.pl');
        define('URL', HOST . URI);
        if (!file_exists(BASE.'/logs/' . date('y-m-d')))
            if (!mkdir($concurrentDirectory = BASE . '/logs/' . date('y-m-d'), 777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        define('LOG_PATH',BASE.'/logs/' . date('y-m-d'));
        if (!file_exists(BASE.'/logs_DB/' . date('y-m-d')))
            if (!mkdir($concurrentDirectory = BASE . '/logs_DB/' . date('y-m-d'), 777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        define('LOG_DB', BASE.'/logs_DB/' . date('y-m-d'));
    }
}