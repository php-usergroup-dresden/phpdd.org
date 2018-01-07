<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits;

use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;

/**
 * Trait TicketProviding
 * @package PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits
 */
trait TicketProviding
{
	protected function getConferenceTicket( Money $money ) : Ticket
	{
		return new Ticket(
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Conference Ticket' ),
			new TicketDescription( 'Grants access to the conference sessions.' ),
			new TicketPrice( $money )
		);
	}

	protected function getWorkshopTicket( string $ticketType, string $name, string $description, Money $money ) : Ticket
	{
		return new Ticket(
			new TicketType( $ticketType ),
			new TicketName( $name ),
			new TicketDescription( $description ),
			new TicketPrice( $money )
		);
	}
}
