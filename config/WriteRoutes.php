<?php declare(strict_types=1);

return [
	'^/tickets/select/?$'       => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\SelectTicketsRequestHandler::class,
	'^/tickets/abort/?$'        => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\AbortTicketOrderRequestHandler::class,
	'^/tickets/save-details/?$' => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\SaveTicketDetailsRequestHandler::class,
	'^/tickets/purchase/?$'     => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\PurchaseTicketsRequestHandler::class,
];
