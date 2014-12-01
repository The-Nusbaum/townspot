<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\MediaController' => 'Api\Controller\MediaController',
            'Api\Controller\SeriesController' => 'Api\Controller\SeriesController',
            'Api\Controller\CategoryController' => 'Api\Controller\CategoryController',
            'Api\Controller\UserController' => 'Api\Controller\UserController',
            'Api\Controller\CityController' => 'Api\Controller\CityController',
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
        ),
    ),
    'view_manager' => array(
        'strategies'               => array(
            'ViewJsonStrategy'
        )
    ),
);