<?php
return array(
    'doctrine' => array(
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
    ),
	'google_analytics' => array(
		'id' => 'UA-33048703-1',
		'domain_name'  => 'townspot.tv',
	),
	'zfcuser' => array(
		'zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
		//'user_entity_class' => 'ZfcUser\Entity\User',
		'enable_registration' => true,
		'enable_username' => true,
		'auth_adapters' => array( 100 => 'ZfcUser\Authentication\Adapter\Db' ),
		'enable_display_name' => true,
		'auth_identity_fields' => array( 'email','username' ),
		//'user_login_widget_view_template' => 'zfc-user/user/login.phtml',
		//'allowed_login_states' => array( null, 1 ),
	),
    'bjyauthorize' => array(
        'default_role' => 'Guest',
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'role_providers' => array(
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table'                 => 'user_role',
                'role_id_field'         => 'role_id',
                'parent_role_field'     => 'parent',
            ),
        ),
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'artist'                => array(),
                'admin'                 => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('Artist'), 'artist'),
                    array(array('Administrator'), 'admin'),
                ),
            ),
        ),
    ),
    'zf2-db-session'=>array(
        'sessionConfig' => array(
            'cache_expire' => 86400,
//            'cookie_domain' => 'localhost',
//            'name' => 'localhost',
            'cookie_lifetime' => 1800,
            'gc_maxlifetime' => 1800,
            'cookie_path' => '/',
            'cookie_secure' => TRUE,
            'remember_me_seconds' => 3600,
            'use_cookies' => true,
        ),
        'serviceConfig'=>array(
            'base64Encode'=>false
        )
    ),
    'service_manager' => array(
        'aliases' => array(
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'DBSessionStorage\Storage\DBStorage' => 'DBSessionStorage\Factory\DBStorageFactory',
        ),
    ),
);