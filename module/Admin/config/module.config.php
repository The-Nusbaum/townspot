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
    'router' => array(
        'routes' => array(
            'admin_home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_artists' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/artists',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Artist',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_users' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/users',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_user_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/users/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                    ),
                ),
            ),
            'admin_series' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/series',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_series_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/users/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'add',
                    ),
                ),
            ),
            'admin_video' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Video',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_video_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Video',
                        'action'     => 'add',
                    ),
                ),
            ),
            'admin_video_categories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/videocategories',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Video',
                        'action'     => 'categories',
                    ),
                ),
            ),
            'admin_video_categories_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/videocategories/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Video',
                        'action'     => 'addcategory',
                    ),
                ),
            ),
            'admin_locations' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/locations',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Location',
                        'action'     => 'categories',
                    ),
                ),
            ),
            'admin_locations_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/stages',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Location',
                        'action'     => 'stage',
                    ),
                ),
            ),
            'admin_reporting' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/reporting',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Reporting',
                        'action'     => 'indec',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
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
            'Admin\Controller\Index' => 'Admin\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => APPLICATION_PATH . '/module/Admin/view/layout/layout.phtml',
            'admin/index/index' 	  => APPLICATION_PATH . '/module/Admin/view/admin/index/index.phtml',
            'error/404'               => APPLICATION_PATH . '/module/Admin/view/error/404.phtml',
            'error/index'             => APPLICATION_PATH . '/module/Admin/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            APPLICATION_PATH . '/module/Admin/view',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
