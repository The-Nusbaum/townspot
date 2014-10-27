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
        'servers' => array(
            'static_1' => array(
                'scheme' => 'http',
                'host' => 'images1.townspot.tv',
                'port' => 80
            ),
            'static_2' => array(
                'scheme' => 'http',
                'host' => 'images2.townspot.tv',
                'port' => 80
            ),
            'static_3' => array(
                'scheme' => 'http',
                'host' => 'images3.townspot.tv',
                'port' => 80
            ),
            'static_4' => array(
                'scheme' => 'http',
                'host' => 'images4.townspot.tv',
                'port' => 80
            ),
            'static_5' => array(
                'scheme' => 'http',
                'host' => 'images5.townspot.tv',
                'port' => 80
            ),
            'static_6' => array(
                'scheme' => 'http',
                'host' => 'images6.townspot.tv',
                'port' => 80
            ),
            'static_7' => array(
                'scheme' => 'http',
                'host' => 'images7.townspot.tv',
                'port' => 80
            ),
            'static_8' => array(
                'scheme' => 'http',
                'host' => 'images8.townspot.tv',
                'port' => 80
            ),
            'static_9' => array(
                'scheme' => 'http',
                'host' => 'images9.townspot.tv',
                'port' => 80
            ),
        ),
    ),
	'google_analytics' => array(
		'id' => 'UA-33048703-1',
		'domain_name'  => 'townspot.tv',
	),
    'zf-snap-google-adsense' => array(
		'publisher-id' => 'pub-4550038254482078',
		'ads' => array(
			'leaderboard' => array(
				'id' => 9042539146,
				'size' => '728x90',
			),
			'banner' => array(
				'id' => 5441220349,
				'size' => '468x60',
			),
			'mobile-leaderboard' => array(
				'id' => 2766955547,
				'size' => '320x50',
			),
			'half-banner' => array(
				'id' => 1011020740,
				'size' => '234x60',
			),
			'small-rectangle' => array(
				'id' => 3824886349,
				'size' => '180x50',
			),
			'small-rectangle' => array(
				'id' => 3824886349,
				'size' => '180x50',
			),
		),
        'enable' => true,
        'renderer' => 'zf-snap-google-adsense-renderer-view-asynchronous',
        'unit-limit' => array(
            \ZfSnapGoogleAdSense\Model\AdUnit::TYPE_CONTENT => 99,
            \ZfSnapGoogleAdSense\Model\AdUnit::TYPE_LINK => 99,
        ),
        'renderers' => array(
            'zf-snap-google-adsense-renderer-view-placeholdit' => array(
                'params' => array(
                    'useAdNameToText' => true,
                    'backgroundColor' => 'ccc',
                    'textColor' => '969696',
                    'format' => 'gif',
                ),
            ),
            'zf-snap-google-adsense-renderer-view-html' => array(
                'params' => array(
                    'background' => '#ccc',
                    'color' => '#969696',
                    'style' => '',
                ),
            ),
        ),
    ),
	'zfcuser' => array(
		'zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
		//'user_entity_class' => 'ZfcUser\Entity\User',
		'enable_registration' => true,
		'enable_username' => true,
		'auth_adapters' => array( 
			100 => 'ZfcUser\Authentication\Adapter\Db', 
			99 => 'Townspot\Authentication\Adapter\Db' 
		),
		'enable_display_name' => true,
		'auth_identity_fields' => array( 'email','username' ),
        'login_redirect_route' => 'dashboard',
		//'user_login_widget_view_template' => '/login.phtml',
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
                'guest'                 => array(),
                'unconfirmed'           => array(),
                'user'                  => array(),
                'artist'                => array(),
                'admin'                 => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('Guest'), 'guest'),
                    array(array('Unconfirmed'), 'unconfirmed'),
                    array(array('User'), 'user'),
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
        'abstract_factories' => array(
            'ZfSnapGoogleAdSense\View\Helper\Renderer\ViewFactory' => 'ZfSnapGoogleAdSense\View\Helper\Renderer\ViewFactory',
        ),
        'aliases' => array(
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'DBSessionStorage\Storage\DBStorage' => 'DBSessionStorage\Factory\DBStorageFactory',
        ),
        'invokables' => array(
            'Townspot\Authentication\Adapter\Db' => '\Townspot\Authentication\Adapter\Db',
		),
    ),
    'view_helpers' => array(
        'aliases' => array(
            'adsense' => 'googleAdSense',
        ),
        'factories' => array(
            'googleAdSense' => 'ZfSnapGoogleAdSense\View\Helper\GoogleAdSenseFactory',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'zf-snap-google-adsense-renderer-view-asynchronous' => APPLICATION_PATH . '/src/Townspot/Adsense/view/asynchronous.phtml',
            'zf-snap-google-adsense-renderer-view-html'         => APPLICATION_PATH . '/src/Townspot/Adsense/view//html.phtml',
            'zf-snap-google-adsense-renderer-view-placeholdit'  => APPLICATION_PATH . '/src/Townspot/Adsense/view/placeholdit.phtml',
            'zf-snap-google-adsense-renderer-view-synchronous'  => APPLICATION_PATH . '/src/Townspot/Adsense/view/synchronous.phtml',
        ),
    ),
);