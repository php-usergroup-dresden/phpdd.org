<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Invoices;

abstract class InvoiceTypes
{
	public const ORDER  = 'order';

	public const REFUND = 'refund';

	public const ALL    = [
		self::ORDER,
		self::REFUND,
	];
}