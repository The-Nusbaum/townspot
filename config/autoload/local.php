<?php
$db = array(
'host'     => '216.157.108.165',
                'port'     => '3306',
                'user'     => 'tsz_user',
                'password' => 'sh@Fnrt1ps0',
                'dbname'   => 'tsz',
);

$amqp = array(
    'host' => '216.157.108.165',
    'port' => '5672',
    'user' => 'amqpUser',
    'pass' => 'ermahgerd!',
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
