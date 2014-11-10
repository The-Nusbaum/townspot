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
            'admin-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin-users' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/users',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                        'type' 		 => 'User',
                    ),
                ),
            ),
            'admin-user-add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                        'type' 		 => 'User',
                    ),
                ),
            ),
            'admin-user-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user/edit',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-user-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'admin-admins' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/admins',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                        'type' 		 => 'Administrator',
                    ),
                ),
            ),
            'admin-admin-add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/admin/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                        'type' 		 => 'Administrator',
                    ),
                ),
            ),
            'admin-admin-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/admin/edit',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-admin-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/admin/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'admin-artists' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/artists',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                        'type' 		 => 'Artist',
                    ),
                ),
            ),
            'admin-artist-add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                        'type' 		 => 'Artist',
                    ),
                ),
            ),
            'admin-artist-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/artist/edit',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-artist-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/artist/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'admin-series' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/series',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin-add-series' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/series/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'add',
                    ),
                ),
            ),
            'admin-series-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/series/edit',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-series-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/series/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'admin-video' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin-add-video' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'add',
                    ),
                ),
            ),
            'admin-video-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video/edit',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-video-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'admin-videocategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/videocategories',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\MediaCategory',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin-stafffavorites' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/stafffavorites',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Section',
                        'action'     => 'media',
                        'section' 	 => 'favorites',
                    ),
                ),
            ),
            'admin-stage' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/stage',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Section',
                        'action'     => 'media',
                        'section' 	 => 'stage',
                    ),
                ),
            ),
            'admin-spotlight' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/spotlight',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Section',
                        'action'     => 'media',
                        'section' 	 => 'spotlight',
                    ),
                ),
            ),
		)
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
            'Admin\Controller\Index' 		=> 'Admin\Controller\IndexController',
            'Admin\Controller\User'  		=> 'Admin\Controller\UserController',
            'Admin\Controller\Video' 		=> 'Admin\Controller\VideoController',
            'Admin\Controller\Location' 	=> 'Admin\Controller\LocationController',
            'Admin\Controller\Reporting' 	=> 'Admin\Controller\ReportingController',
            'Admin\Controller\Series' 		=> 'Admin\Controller\SeriesController',
            'Admin\Controller\MediaCategory'=> 'Admin\Controller\MediaCategoryController',
            'Admin\Controller\Section' 		=> 'Admin\Controller\SectionController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'admin/layout'            => APPLICATION_PATH . '/module/Admin/view/layout/layout.phtml',
            'admin/index/index' 	  => APPLICATION_PATH . '/module/Admin/view/admin/index/index.phtml',
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
