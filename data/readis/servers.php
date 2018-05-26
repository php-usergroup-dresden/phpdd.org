<?php declare(strict_types=1);

return [
	[
		'name'          => 'Redis-Server 1',
		'host'          => 'phpdd18-redis',
		'port'          => 6379,
		'auth'          => null,
		'timeout'       => 2.5,
		'retryInterval' => 100,
		'databaseMap'   => [
			0 => 'Sessions',
		],
	],
];