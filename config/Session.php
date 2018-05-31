<?php declare(strict_types=1);

return [
	'sessionHandler' => [
		'name'        => 'PHPDD18SID',
		'handler'     => 'redis',
		'savePath'    => 'tcp://phpdd18-redis:6379?database=0&weight=1',
		'maxLifetime' => 86400,
	],
	'cookieSettings' => [
		'lifetime' => 86400,
		'path'     => '/',
		'domain'   => '',
		'secure'   => false,
		'httpOnly' => true,
	],
];
