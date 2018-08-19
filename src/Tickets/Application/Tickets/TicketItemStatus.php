<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

abstract class TicketItemStatus
{
	public const PAID     = 'paid';

	public const REFUNDED = 'refunded';
}