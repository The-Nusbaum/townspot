<?php
$db = array(
		'host'     => 'localhost',
		'port'     => '3306',
		'user'     => 'root',
		'password' => '',
		'dbname'   => 'tsz',
);

$zfcuser_settings = array(
    'zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
    //'user_entity_class' => 'ZfcUser\Entity\User',
    'enable_registration' => true,
    'enable_username' => true,
    'auth_adapters' => array( 100 => 'ZfcUser\Authentication\Adapter\Db' ),
    'enable_display_name' => true,
    'auth_identity_fields' => array( 'email','username' ),
    //'user_login_widget_view_template' => 'zfc-user/user/login.phtml',
    //'allowed_login_states' => array( null, 1 ),
);

return array(
    'db' => array_merge(array( 'driver'    => 'PdoMysql'),$db),
    'doctrine' => array(
        'connection' => array(
			'orm_default' => array(
				'params' => array_merge(array( 'driver'    => 'pdo_mysql'),$db),
			),
		),
		'entityConfigPath' => APPLICATION_PATH . "/config/xml",
		'cacheClass'       => "\Doctrine\Common\Cache\ArrayCache",
		'proxy'	=> array(
			'path'					=> APPLICATION_PATH . "/data/proxies",
			'namespace'				=> "Townspot_Doctrine_Proxies",
			'enableAutoGenerate'	=> true,
		),
	),
    'lucene' => array(
		'path' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'lucene',
	),
    'cdn_light' => array(
        'head_link' => false,
        'head_script' => false,
        'link_cdn' => true, 
        'global' => array(
        ),
        'servers' => array(
            'static_1' => array(
                'scheme' => 'http',
                'host' => 'dev.tsz.com',
                'port' => 80
            ),
        ),
    ),
	'google_analytics' => array(
		'id' => 'UA-33048703-1',
		'domain_name'  => 'townspot.tv',
	),
	'zfcuser' => $zfcuser_settings,
    'service_manager' => array(
        'aliases' => array(
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
