<?php
$db = array(
		'host'     => 'localhost',
		'port'     => '3306',
		'user'     => 'root',
		'password' => '',
		'dbname'   => 'tsz',
);

return array(
    'db' => array_merge(array( 'driver'    => 'PdoMysql'),$db),
    'doctrine' => array(
        'connection' => array(
			'orm_default' => array(
				'params' => array_merge(array( 'driver'    => 'pdo_mysql'),$db),
			),
		),
	),
    'zf-snap-google-adsense' => array(
        'renderer' => 'zf-snap-google-adsense-renderer-view-html',
    ),
);
