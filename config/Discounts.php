<?php declare(strict_types=1);

return [
	'PHPUGDD member discount'   => [
		'description'    => 'Discount for PHP USERGROUP DRESDEN e.V. members',
		'discount'       => -3000,
		'allowedTickets' => [
			'Workshop Ticket Slot A1',
			'Workshop Ticket Slot A2',
			'Workshop Ticket Slot B1',
			'Workshop Ticket Slot B2',
			'Workshop Ticket Slot C1',
			'Conference Ticket',
		],
		'codes'          => [
			'X930372K',
			'N200476A',
			'E960551O',
		],
	],
	'Free UG conference ticket' => [
		'description'    => 'Free ticket for attendees of a user group',
		'discount'       => -9900,
		'allowedTickets' => [
			'Conference Ticket',
		],
		'codes'          => [
			'X930372K',
			'N200476A',
		],
	],
];
