<?php
$db = array(
'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'root',
                'password' => '',
                'dbname'   => 'tsz',
);

$amqp = array(
    'host' => 'localhost',
    'port' => '5672',
    'user' => 'guest',
    'pass' => 'guest',
    'vhost' => '/'
);

$encoding = array(
    'pass' => 'McXHT8g3ieiqPoJTFCNt',
    'host' => '216.157.108.165',
    'sshHost' => getHostByName(getHostName()) 
);

return array(
    'db' => array_merge(array( 'driver'    => 'PdoMysql'),$db),
    'amqp' => $amqp,
    'encoding' => $encoding,
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
