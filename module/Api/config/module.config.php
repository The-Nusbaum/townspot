<?php
return array(
    'controllers' => array(
        'invokables' => array(
//          'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\MediaController' => 'Api\Controller\MediaController',
            'Api\Controller\SeriesController' => 'Api\Controller\SeriesController',
            'Api\Controller\CategoryController' => 'Api\Controller\CategoryController',
            'Api\Controller\UserController' => 'Api\Controller\UserController',
            'model' => 'Api\Controller\ModelController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'custom_methods' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api/[:model[/:method[/:id]]]',
                    'defaults' => array(
                        'controller' => 'model'
                    ),
                ),
            ),
/*
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
*/
        ),
    ),
    'view_manager' => array(
        'strategies'               => array(
            'ViewJsonStrategy'
        )
    ),
);