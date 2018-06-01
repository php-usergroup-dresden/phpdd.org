<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Generator;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesSelectedTicketInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use Throwable;

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

	/**
	 * @return ProvidesSelectedTicketInformation[]|Generator
	 */
	public function getTickets() : Generator
	{
		foreach ( $this->getSelectedTicketConfigs() as $selectedTicketConfig => $quantity )
		{
			yield new SelectedTicketInfo( $selectedTicketConfig, $quantity );
		}
	}

	private function getSelectedTicketConfigs() : Generator
	{
		foreach ( $this->selectedTickets as $id => $quantity )
		{
			if ( 0 === (int)$quantity )
			{
				continue;
			}

			$ticketId     = new TicketId( $id );
			$ticketConfig = $this->getTicketConfigById( $ticketId );

			if ( null === $ticketConfig )
			{
				continue;
			}

			yield $ticketConfig => (int)$quantity;
		}
	}

	private function getTicketConfigById( TicketId $ticketId ) : ?TicketConfig
	{
		try
		{
			return $this->ticketsConfig->findTicketById( $ticketId );
		}
		catch ( Throwable $e )
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