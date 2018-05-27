<?php declare(strict_types=1);

return [
	'^/tickets/$'         => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\TicketSelectionRequestHandler::class,
	'^/tickets/details/$' => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\TicketDetailsRequestHandler::class,
];
