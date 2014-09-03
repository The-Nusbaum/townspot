<?php
return array(
    'view_helpers' => array(
        'factories' => array(
            'headLinkCdn' => 'CdnLight\View\Helper\Service\HeadLinkCdnFactory',
            'headScriptCdn' => 'CdnLight\View\Helper\Service\HeadScriptCdnFactory',
            'linkCdn' => 'CdnLight\View\Helper\Service\LinkCdnFactory',
        ),
        'aliases' => array(
            'headLink' => 'headLinkCdn',
            'headScript' => 'headScriptCdn',
        ),
    ),
	'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                APPLICATION_PATH . '/public/css',
                APPLICATION_PATH . '/data/assets',
				VENDOR_PATH,
				APPLICATION_PATH . '/components',
            ),
        ),
		'caching' => array(
            'default' => array(
                'cache'     => 'AssetManager\\Cache\\FilePathCache',
                'options' => array(
					'dir' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'static',
                ),
            ),
        ),		
		'filters' => array(
			'less' => array(
				array(
					'filter' => 'LessphpFilter',
				),
			),
			'css' => array(
				array(
					'filter' => 'CssMinFilter',
				),
			),
//            'js' => array(
//                array(
//                    'filter' => 'JSMin',
//                ),
//            ),
		),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'cdnLinkBuilderContainer' => 'CdnLight\Generator\Service\LinkBuilderContainerFactory',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => APPLICATION_PATH . '/module/Application/view/layout/layout.phtml',
            'application/index/index' => APPLICATION_PATH . '/module/Application/view/application/index/index.phtml',
            'error/404'               => APPLICATION_PATH . '/module/Application/view/error/404.phtml',
            'error/index'             => APPLICATION_PATH . '/module/Application/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            APPLICATION_PATH . '/module/Application/view',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
