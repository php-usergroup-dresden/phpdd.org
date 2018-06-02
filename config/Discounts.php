<?php declare(strict_types=1);

$phpugddMemberCodes = [
	'E11318494Z',
	'A97218901G',
	'D87318324E',
	'X32718057T',
];

$phpugSupporterCodes = [
	'Z00918356Z',
	'L34818444E',
	'P95318357E',
];

return [
	'10% PHPUGDD member discount full-day workshop' => [
		'description'    => 'As a member of the PHP USERGROUP DRESDEN e.V. you pay 10% less for a full-day workshop ticket.',
		'discount'       => -2490,
		'allowedTickets' => [
			'PHPDD18-WS-01',
			'PHPDD18-WS-02',
		],
		'codes'          => $phpugddMemberCodes,
	],
	'10% PHPUGDD member discount half-day workshop' => [
		'description'    => 'As a member of the PHP USERGROUP DRESDEN e.V. you pay 10% less for a half-day workshop ticket.',
		'discount'       => -1490,
		'allowedTickets' => [
			'PHPDD18-WS-03',
			'PHPDD18-WS-04',
			'PHPDD18-WS-05',
			'PHPDD18-WS-06',
		],
		'codes'          => $phpugddMemberCodes,
	],
	'10% PHPUGDD member discount conference'        => [
		'description'    => 'As a member of the PHP USERGROUP DRESDEN e.V. you pay 10% less a the conference ticket.',
		'discount'       => -1190,
		'allowedTickets' => [
			'PHPDD18-CT-01',
		],
		'codes'          => $phpugddMemberCodes,
	],
	'50% discount on conference ticket'             => [
		'description'    => 'As an attendee of a supporting user group, you pay half the price for the conference ticket!',
		'discount'       => -5950,
		'allowedTickets' => [
			'PHPDD18-CT-01',
		],
		'codes'          => $phpugSupporterCodes,
	],
];
