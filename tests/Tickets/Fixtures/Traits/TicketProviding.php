<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

/**
 * Trait TicketProviding
 * @package PHPUGDD\PHPDD\Website\Tickets\Tests\Fixtures\Traits
 */
trait TicketProviding
{
	/**
	 * @param \Money\Money $money
	 *
	 * @return \PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 */
	protected function getConferenceTicket( Money $money ) : Ticket
	{
		return new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Conference Ticket' ),
			new TicketDescription( 'Grants access to the conference sessions.' ),
			new TicketPrice( $money )
		);
	}

	/**
	 * @param string       $ticketType
	 * @param string       $name
	 * @param string       $description
	 * @param \Money\Money $money
	 *
	 * @return \PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 */
	protected function getWorkshopTicket( string $ticketType, string $name, string $description, Money $money ) : Ticket
	{
		return new Ticket(
			new TicketId( 'PHPDD18-WS-01' ),
			new TicketType( $ticketType ),
			new TicketName( $name ),
			new TicketDescription( $description ),
			new TicketPrice( $money )
		);
	}
}
