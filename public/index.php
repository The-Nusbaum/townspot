<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__DIR__)));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
defined('VENDOR_PATH') || define('VENDOR_PATH', realpath(APPLICATION_PATH . '/vendor'));
defined('APPLICATION_NAMESPACE') || define('APPLICATION_NAMESPACE', 'Townspot');
error_reporting(1);

date_default_timezone_set('UTC');
chdir(dirname(__DIR__));
require 'init_autoloader.php';
Zend\Mvc\Application::init(require APPLICATION_PATH . '/config/application.config.php')->run();