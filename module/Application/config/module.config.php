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
        'invokables' => array(  
            'VideoPlayer' 	=> 'Townspot\View\Helper\VideoPlayer',
            'VideoCarousel' => 'Townspot\View\Helper\VideoCarousel',
            'VideoBlock' 	=> 'Townspot\View\Helper\VideoBlock',
            'img' => 'Townspot\View\Helper\Image',
            'AddThisLinks' 	=> 'Townspot\View\Helper\AddThisLinks',
        ),
    ),
	'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                APPLICATION_PATH . '/public/js',
                APPLICATION_PATH . '/public/css',
                APPLICATION_PATH . '/public/less',
                APPLICATION_PATH . '/data/assets',
				VENDOR_PATH,
				APPLICATION_PATH . '/components',
            ),
            'collections' => array(
                'css/townspot.css' => array(
                    'xxs.less',
                    'socialmedia.css',
                    'style.less',
                ),
                'css/tinymce/skin.min.css' => array(
                    'tinymce/tinymce/skins/lightgray/skin.min.css'
                ),
                'css/tinymce/content.min.css' => array(
                    'tinymce/tinymce/skins/lightgray/content.min.css'
                ),
                'css/tinymce/fonts/tinymce.woff' => array(
                    'tinymce/tinymce/skins/lightgray/fonts/tinymce.woff'
                ),
                'css/tinymce/fonts/tinymce.ttf' => array(
                    'tinymce/tinymce/skins/lightgray/fonts/tinymce.ttf'
                ),
                'css/tinymce/fonts/tinymce.svg' => array(
                    'tinymce/tinymce/skins/lightgray/fonts/tinymce.svg'
                ),
                'js/townspot.js' => array(
                    'kalenjordan/jquery-cookie/jquery.cookie.js',
					'kalenjordan/jquery-cookie/jquery.cookie.js',
					'carousel.js',
					'infobutton.js',
					'togglebuttons.js',
					'geolocation.js',
					'expander.js',
                    //'moxiecode/plupload/js/plupload.min.js',
                    'tinymce/tinymce/tinymce.js',

                ),
                'js/tinymceTheme.js' => array(
                    'tinymce/tinymce/themes/modern/theme.min.js'
                ),
                'js/userEdit.js' => array(
                    'userEdit.js'
                )
            ),			
        ),
/*
		'caching' => array(
            'default' => array(
                'cache'     => 'AssetManager\\Cache\\FilePathCache',
                'options' => array(
					'dir' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'static',
                ),
            ),
        ),		
*/
		'filters' => array(
			'css' => array(
				array(
					'filter' => 'CssMinFilter',
					'filter' => 'LessphpFilter',
				),
			),
            'js' => array(
                array(
                    'filter' => 'JSMin',
                ),
            ),
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
            'search' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/search',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Search',
                        'action'     => 'index',
                    ),
                ),
            ),
			'video_player' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/videos/:id[/:title]',
					'constraints' => array(
						'id' => '\d+',
						'title' => '[a-zA-Z0-9_-]+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action'     => 'player',
					),
				),
			),	
			'video_embed' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/[videos/]embed/:id[/:title]',
					'constraints' => array(
						'id' => '\d+',
						'title' => '[a-zA-Z0-9_-]+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action'     => 'embed',
					),
				),
			),	
			'add-video' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/add-video',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Video',
                        'action'     => 'upload',
                    ),
                ),
			),
			'upload' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/videos/upload',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Video',
                        'action'     => 'upload',
                    ),
                ),
			),
			'video_edit' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/edit/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'edit'
					)
				),			
			),
			'video_delete' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/delete/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'delete'
					)
				),			
			),
			'videos_related' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/related/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'related'
					)
				),			
			),
			'videos_ratings' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/ratings/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'ratings'
					)
				),			
			),
			'video_comments' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/comments/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'comments'
					)
				),			
			),			
			'video_success' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route' => '/videos/success',
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'success'
					)
				),			
			),
			'video_flag' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route' => '/videos/flag',
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'flag'
					)
				),			
			),
			'videos_add_rating' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/ratings/add/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'addrating'
					)
				),			
			),
			'video_add_comment' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/comments/add/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'addcomment'
					)
				),			
			),			
			'video_remove_comment' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/videos/comments/remove/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'removecomment'
					)
				),			
			),			
			'discover' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/discover[/:param1][/:param2][/:param3][/:param4][/:param5][/:param6][/:param7][/:param8][/:param9][/:param10]',
					'constraints' => array(
						'param1' => '[a-zA-Z0-9_-]+',
						'param2' => '[a-zA-Z0-9_-]+',
						'param3' => '[a-zA-Z0-9_-]+',
						'param4' => '[a-zA-Z0-9_-]+',
						'param5' => '[a-zA-Z0-9_-]+',
						'param6' => '[a-zA-Z0-9_-]+',
						'param7' => '[a-zA-Z0-9_-]+',
						'param8' => '[a-zA-Z0-9_-]+',
						'param9' => '[a-zA-Z0-9_-]+',
						'param10' => '[a-zA-Z0-9_-]+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Video',
						'action' => 'index'
					)
				),			
			),			
            'stage' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/watch[/:param1][/:param2][/:param3][/:param4][/:param5][/:param6][/:param7][/:param8][/:param9][/:param10]',
					'constraints' => array(
						'param1' => '[a-zA-Z0-9_-]+',
						'param2' => '[a-zA-Z0-9_-]+',
						'param3' => '[a-zA-Z0-9_-]+',
						'param4' => '[a-zA-Z0-9_-]+',
						'param5' => '[a-zA-Z0-9_-]+',
						'param6' => '[a-zA-Z0-9_-]+',
						'param7' => '[a-zA-Z0-9_-]+',
						'param8' => '[a-zA-Z0-9_-]+',
						'param9' => '[a-zA-Z0-9_-]+',
						'param10' => '[a-zA-Z0-9_-]+',
					),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'stage',
                    ),
                ),
            ),
			'dashboard' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'index',
                    ),
                ),
			),
            'userEdit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user/edit',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'userEdit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user/manageseries',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'manageseries',
                    ),
                ),
            ),
			'profile_short' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/u/:id',
					'constraints' => array(
						'id' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'profile',
					),
				),
			),	
			'profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/profile/:username',
					'constraints' => array(
						'username' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'profile',
					),
				),
			),	
			'forgot_password' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/forgot_password',
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'forgotpassword',
					),
				),
			),	
			'reset_password' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/reset_password/:id',
					'constraints' => array(
						'id' => '[a-zA-Z0-9_-]+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'resetpassword',
					),
				),
			),	
			'verify' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/verify/:id',
					'constraints' => array(
						'id' => '[a-zA-Z0-9_-]+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'verify',
					),
				),
			),
			'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/login',
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action'     => 'login',
					),
				),
			),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'logout',
                    ),
                ),
            ),
			'series_player' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/series[/:name[/:season[/:episode]]]',
					'constraints' => array(
						'name' => '[a-zA-Z0-9_-]+',
						'season' => '\d+',
						'episode' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Series',
						'action'     => 'index',
					),
				),
			),	
			'series_embed' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route'    => '/series/embed/:name[/:season[/:episode]]',
					'constraints' => array(
						'name' => '[a-zA-Z0-9_-]+',
						'season' => '\d+',
						'episode' => '\d+',
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Series',
						'action'     => 'index',
					),
				),
			),	
			'what_is_townspot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/what-is-townspot',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'about',
					),
				),
			),	
			'what_makes_townspot_different' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/what-makes-townspot-different',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'different',
					),
				),
			),	
			'privacy' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/privacy',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'privacy',
					),
				),
			),	
			'terms' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/terms-conditions',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'terms',
					),
				),
			),	
			'agreement' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/video-submission-agreement',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'agreement',
					),
				),
			),	
			'standards' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/community-standards',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'standards',
					),
				),
			),	
			'policy' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/content-policy',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'policy',
					),
				),
			),	
			'tips' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/video-submission-tips',
					'defaults' => array(
						'controller' => 'Application\Controller\StaticPage',
						'action'     => 'tips',
					),
				),
			),	
			'contact' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/contact-us',
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action'     => 'contact',
					),
				),
			),	
			'explore_link' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/getexplorelink',
					'defaults' => array(
						'controller' => 'Application\Controller\Ajax',
						'action'     => 'explorelink',
					),
				),
			),	
			'admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/admin',
					'defaults' => array(
						'controller' => 'Admin\Controller\Index',
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
            'cdnLinkBuilderContainer' 	=> 'CdnLight\Generator\Service\LinkBuilderContainerFactory',
			'navigation' 			    => 'Zend\Navigation\Service\DefaultNavigationFactory',
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
            'Application\Controller\Index' 		=> 'Application\Controller\IndexController',
            'Application\Controller\Video' 		=> 'Application\Controller\VideoController',
            'Application\Controller\Search' 	=> 'Application\Controller\SearchController',
            'Application\Controller\User' 		=> 'Application\Controller\UserController',
            'Application\Controller\StaticPage' => 'Application\Controller\StaticPageController',
            'Application\Controller\Ajax' 		=> 'Application\Controller\AjaxController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'application/layout'      => APPLICATION_PATH . '/module/Application/view/layout/layout.phtml',
            'application/index/index' => APPLICATION_PATH . '/module/Application/view/application/index/index.phtml',
            'error/404'               => APPLICATION_PATH . '/module/Application/view/error/404.phtml',
            'error/index'             => APPLICATION_PATH . '/module/Application/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            APPLICATION_PATH . '/module/Application/view',
            'zfcuser' => APPLICATION_PATH . '/module/Application/view',
        ),
    ),
    'module_layouts' => array(
        'ZfcUser' => 'application/layout',
    ),
	'navigation' => array(
		'default' => array(
			array(
				'label' 		=> '<img src="/img/pin_logo.png"> Town<span class="highlight">Spot</span>.tv',
                'route' 		=> 'home',
				'class'			=> 'nav-logo hidden-xs',
            ),
			array(
				'label' 		=> 'Discover',
                'route' 		=> 'discover',
            ),
			array(
				'label' 		=> 'Upload',
                'route' 		=> 'add-video',
            ),
			array(
				'label' 		=> 'Backstage',
                'uri' 			=> 'http://backstage.townspot.tv/',
				'target'	 	=> '_blank',
            ),
			array(
				'label' 		=> 'What is Town<span class="highlight">Spot</span>?',
                'route' 		=> 'what_is_townspot',
				'class'			=> 'hidden-sm',
            ),
			array(
				'label' 		=> 'View Profile',
                'route' 		=> 'dashboard',
				'class'			=> 'visible-xs',
				'resource'		=> 'unconfirmed',
            ),
			array(
				'label' 		=> '<i class="fa fa-cogs"></i> Admin',
                'route' 		=> 'admin',
				'class'			=> 'visible-xs',
				'resource'		=> 'admin',
            ),
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
