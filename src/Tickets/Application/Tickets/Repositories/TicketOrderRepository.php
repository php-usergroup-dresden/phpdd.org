<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories;

use PDO;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;

final class TicketOrderRepository implements ProvidesReservedTicketCount
{
	/** @var PDO */
	private $database;

	public function __construct( PDO $database )
	{
		$this->database = $database;
	}

	public function getReservedCount( Ticket $ticket ) : int
	{
		// TODO: Query the database for real reserved count

		return 23;
	}
}