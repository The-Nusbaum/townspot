<?php
$db = include('local.dist.php');

return array(
    'db' => array_merge(array( 'driver'    => 'PdoMysql'),$db),
    'doctrine' => array(
        'connection' => array(
			'orm_default' => array(
				'params' => array_merge(array( 'driver'    => 'pdo_mysql'),$db),
			),
		),
	),
    'cdn_light' => array(
        'servers' => array(
            'static_1' => array(
                'scheme' => 'http',
                'host' => 'images.townspot.tv',
                'port' => 80
            ),
        ),
    ),
);
