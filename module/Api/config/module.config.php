<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'Api' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/api',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/api/[:controller[/:action]]',
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
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'api/layout'           => APPLICATION_PATH . '/module/Api/view/layout/layout.phtml',
            'api/index/index' 	   => APPLICATION_PATH . '/module/Api/view/api/index/index.phtml',
        ),
        'template_path_stack' => array(
            'api' => APPLICATION_PATH . '/module/Api/view',
        ),
    ),
);