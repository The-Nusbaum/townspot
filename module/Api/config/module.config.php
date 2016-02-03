<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\MediaController' => 'Api\Controller\MediaController',
            'Api\Controller\SeriesController' => 'Api\Controller\SeriesController',
            'Api\Controller\CategoryController' => 'Api\Controller\CategoryController',
            'Api\Controller\UserController' => 'Api\Controller\UserController',
            'Api\Controller\CityController' => 'Api\Controller\CityController',
            'Api\Controller\MediaCommentController' => 'Api\Controller\MediaCommentController',
            'Api\Controller\ProvinceController' => 'Api\Controller\ProvinceController',
            'Api\Controller\TrackingController' => 'Api\Controller\TrackingController',
            'model' => 'Api\Controller\ModelController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'click_track' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api/tracking/record-click/[:type]/[:user][/:value]',
                    'defaults' => array(
                        'controller'    => 'Api\Controller\TrackingController',
                        'action'        => 'recordClick'
                    )
                )
            ),
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