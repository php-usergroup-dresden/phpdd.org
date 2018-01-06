<?php declare(strict_types=1);

return [
	'sessionHandler' => [
		'name'        => 'PHPDDSID',
		'handler'     => 'files',
		'savePath'    => sys_get_temp_dir(),
		'maxLifetime' => 86400,
	],
	'cookieSettings' => [
		'lifetime' => 86400,
		'path'     => '/',
		'domain'   => '127.0.0.1:8018',
		'secure'   => false,
		'httpOnly' => true,
	],
];
