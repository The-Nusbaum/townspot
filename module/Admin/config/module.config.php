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
            'admin-lookup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/lookup',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'lookup',
                    ),
                ),
            ),
            'event-lookup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/lookupevent',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'lookupevent',
                    ),
                ),
            ),
            'event-save' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/saveevent',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'saveevent',
                    ),
                ),
            ),
            'send-message' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/saveevent',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'saveevent',
                    ),
                ),
            ),
            'admin-record-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/delete',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'deleterecord',
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
            'admin-user_ajax' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/lookupusers',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'userlist',
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
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/user/edit/:id',
					'constraints' => array(
						'id' => '\d+',
					),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-user-show' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/admin/user/show/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Admin\Controller\User',
						'action'     => 'show',
					),
				),
            ),
            'admin-series-show' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/admin/series/show/:id',
					'constraints' => array(
						'id' => '\d+',
						'title' => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Admin\Controller\Series',
						'action'     => 'show',
					),
				),
            ),
            'admin-video-show' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/admin/video/show/:id',
					'constraints' => array(
						'id' => '\d+',
						'title' => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Admin\Controller\Media',
						'action'     => 'show',
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
            'admin-administrator-add' => array(
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
                    'route'    => '/admin/artist/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                        'type' 		 => 'Artist',
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
            'admin-video_ajax' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/lookupmedia',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'medialist',
                    ),
                ),
            ),
            'admin-video-available' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/availablemedia',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'mediasearch',
                    ),
                ),
            ),
            'admin-video-approve' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/mediaapprove/[:id]',
                    'constraints' => array(
                        'id'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'mediaupdate',
                        'type' 		 => 'approve',
                    ),
                ),
            ),
            'admin-video-unapprove' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/mediaunapprove/[:id]',
                    'constraints' => array(
                        'id'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'mediaupdate',
                        'type' 		 => 'unapprove',
                    ),
                ),
            ),
            'admin-update-section' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/updatesection',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'updatesection',
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
            'admin-series_ajax' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/lookupseries',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'serieslist',
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
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/series/edit/:id',
                    'constraints' => array(
                        'id'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Series',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'admin-series-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/video/thumbs/:id',
                    'constraints' => array(
                        'id'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'thumbs',
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
            'admin-review-video' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/video/review',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'review',
                    ),
                ),
            ),
            'admin-video-edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/video/edit/:id',
                    'constraints' => array(
                        'id'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Media',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'pop-random' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/admin/ajax/pop-random[/:limit]',
                    'constraints' => array(
                        'limit'  => '[0-9]*'
                    ),
                    'defaults' => array(
                        'limit' => 5,
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'popRandom',
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
                        'section' 	 => 'Staff Favorites',
                        'randomPop'  => 9
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
                        'section' 	 => 'On Screen',
                        'randomPop'  => 5
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
                        'section' 	 => 'Daily Highlights',
                        'randomPop'  => 5
                    ),
                ),
            ),
            'encoding-finished' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/encoding/finished',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ajax',
                        'action'     => 'encodingFinished',
                    ),
                ),
            ),
             'click-tracking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/tracking',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Reporting',
                        'action'     => 'tracking',
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
            'Admin\Controller\Media' 		=> 'Admin\Controller\MediaController',
            'Admin\Controller\Location' 	=> 'Admin\Controller\LocationController',
            'Admin\Controller\Reporting' 	=> 'Admin\Controller\ReportingController',
            'Admin\Controller\Series' 		=> 'Admin\Controller\SeriesController',
            'Admin\Controller\MediaCategory'=> 'Admin\Controller\MediaCategoryController',
            'Admin\Controller\Section' 		=> 'Admin\Controller\SectionController',
            'Admin\Controller\Ajax' 		=> 'Admin\Controller\AjaxController',
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
            'admin/empty'             => APPLICATION_PATH . '/module/Admin/view/layout/empty.phtml',
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
