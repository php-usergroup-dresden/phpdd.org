<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Generator;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class SelectedTicketInfos
{
	use MoneyProviding;

	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var array */
	private $selectedTickets;

	public function __construct( TicketsConfig $ticketsConfig, array $selectedTickets )
	{
		$this->ticketsConfig   = $ticketsConfig;
		$this->selectedTickets = $selectedTickets;
	}

	public function getTickets() : Generator
	{
		foreach ( $this->getSelectedTicketConfigs() as $selectedTicketConfig => $quantity )
		{
			yield new SelectedTicketInfo( $selectedTicketConfig, $quantity );
		}
	}

	private function getSelectedTicketConfigs() : Generator
	{
		foreach ( $this->selectedTickets as $type => $tickets )
		{
			$ticketType = new TicketType( $type );
			foreach ( $tickets as $name => $quantity )
			{
				if ( 0 === (int)$quantity )
				{
					continue;
				}

				$ticketName   = new TicketName( $name );
				$ticketConfig = $this->getTicketConfigByTypeAndName( $ticketType, $ticketName );

				if ( null === $ticketConfig )
				{
					continue;
				}

				yield $ticketConfig => (int)$quantity;
			}
		}
	}

	private function getTicketConfigByTypeAndName( TicketType $ticketType, TicketName $ticketName ) : ?TicketConfig
	{
		try
		{
			return $this->ticketsConfig->findTicketConfigByTypeAndName( $ticketType, $ticketName );
		}
		catch ( TicketConfigNotFoundException $e )
		{
			return null;
		}
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return Money
	 */
	public function getTotalPrice() : Money
	{
		$total = $this->getMoney( 0 );

		/** @var SelectedTicketInfo $ticket */
		foreach ( $this->getTickets() as $ticket )
		{
			$total = $total->add( $ticket->getPrice()->getMoney()->multiply( $ticket->getQuantity() ) );
		}

		return $total;
	}
}