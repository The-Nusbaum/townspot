<?php
return array(
    'modules' => array(
        //'ZendDeveloperTools',
        'DoctrineModule',
        'DoctrineORMModule',
        'AssetManager',
        'EdpModuleLayouts',
        'ZfcBase',
        'ZfcUser',
        'LfjOpauth',
        'Application',
        'Admin',
        'Api',
		'BjyAuthorize',		
		'CdnLight',
		'SlmGoogleAnalytics',	
		'ZfSnapGeoip',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            VENDOR_PATH,
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . '{,*.}{global,local}.php',
            'config/autoload/{,*.}{global,local}.php',
        ),
        'config_cache_enabled' => true,
        'config_cache_key' => 'townspot_config',
        'module_map_cache_enabled' => true,
        'module_map_cache_key' => 'townspot_module_map',
        'cache_dir' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config',
    ),
);
