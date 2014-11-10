<?php
function fatalErrorHandler()
{
   $error = error_get_last();
   if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))
   {
               echo "<pre>";
               print_r($error);
               exit;
   }
}

register_shutdown_function('fatalErrorHandler');

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__DIR__)));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
defined('VENDOR_PATH') || define('VENDOR_PATH', realpath(APPLICATION_PATH . '/vendor'));
defined('APPLICATION_NAMESPACE') || define('APPLICATION_NAMESPACE', 'Townspot');
defined('PROXYPATH') || define('PROXYPATH', APPLICATION_PATH . "/data/proxies");

error_reporting((APPLICATION_ENV == 'development') ? -1 : 0);
error_reporting('E_ALL');
ini_set('display_errors','On');
set_time_limit ( 300 );
date_default_timezone_set('UTC');
chdir(dirname(__DIR__));
require 'init_autoloader.php';
Zend\Mvc\Application::init(require APPLICATION_PATH . '/config/application.config.php')->run();
