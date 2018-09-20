<?php declare(strict_types=1);

return [
	'^/tickets/$'                            => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\TicketSelectionRequestHandler::class,
	'^/tickets/details/$'                    => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\TicketDetailsRequestHandler::class,
	'^/tickets/payment/$'                    => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\TicketPaymentRequestHandler::class,
	'^/tickets/paypal-canceled/?$'           => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\PaypalCancelRequestHandler::class,
	'^/tickets/done/(?<ticketOrderId>.*)/?$' => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\PurchaseDoneRequestHandler::class,
	'^/tickets/scan/?$'                      => \PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read\FindTicketRequestHandler::class,
];
