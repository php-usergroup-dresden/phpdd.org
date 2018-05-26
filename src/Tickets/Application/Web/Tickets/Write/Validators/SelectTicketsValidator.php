<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Generator;
use hollodotme\FluidValidator\MessageCollectors\GroupedListMessageCollector;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use function array_sum;
use function is_array;

final class SelectTicketsValidator
{
	/** @var UserInput */
	private $userInput;

	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var GroupedListMessageCollector */
	private $messages;

	public function __construct( UserInput $userInput, TicketsConfig $ticketsConfig )
	{
		$this->userInput     = $userInput;
		$this->ticketsConfig = $ticketsConfig;
		$this->messages      = new GroupedListMessageCollector();
	}

	private function reset() : void
	{
		$this->messages->clearMessages();
	}

	/**
	 * @return bool
	 */
	public function failed() : bool
	{
		$this->reset();

		if ( !is_array( $this->userInput->getValueToValidate( 'quantity' ) ) )
		{
			$this->messages->addMessage( ['general' => 'Please select at least one ticket.'] );

			return true;
		}

		if ( $this->noTicketWasSelected( $this->userInput->getValueToValidate( 'quantity' ) ) )
		{
			$this->messages->addMessage( ['general' => 'Please select at least one ticket.'] );

			return true;
		}

		if ( $this->notAvailableTicketsSelected( $this->userInput->getValueToValidate( 'quantity' ) ) )
		{
			$this->messages->addMessage( ['general' => 'Please select only currently available tickets.'] );

			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function passed() : bool
	{
		return !$this->failed();
	}

	private function noTicketWasSelected( array $quantities ) : bool
	{
		$countSelected = 0;
		foreach ( $quantities as $type => $tickets )
		{
			if ( is_array( $tickets ) )
			{
				$countSelected += array_sum( $tickets );
			}
		}

		return $countSelected === 0;
	}

	/**
	 * @param array $quantities
	 *
	 * @return bool
	 */
	private function notAvailableTicketsSelected( array $quantities ) : bool
	{
		$countNotAvailable = 0;

		try
		{
			foreach ( $this->getTicketConfigs( $quantities ) as $ticketConfig )
			{
				$countNotAvailable += (int)!$ticketConfig->isAvailable();
			}
		}
		catch ( TicketConfigNotFoundException | InvalidArgumentException $e )
		{
			$countNotAvailable++;
		}

		return $countNotAvailable > 0;
	}

	/**
	 * @param array $quantities
	 *
	 * @throws TicketConfigNotFoundException
	 * @throws InvalidArgumentException
	 *
	 * @return TicketConfig[]|Generator
	 */
	private function getTicketConfigs( array $quantities ) : Generator
	{
		foreach ( $quantities as $type => $tickets )
		{
			if ( !is_array( $tickets ) )
			{
				continue;
			}

			$ticketType = new TicketType( $type );

			foreach ( $tickets as $name => $quantity )
			{
				if ( $quantity === 0 )
				{
					continue;
				}

				$ticketName = new TicketName( $name );

				yield $this->ticketsConfig->findTicketConfigByTypeAndName( $ticketType, $ticketName );
			}
		}
	}

	public function getMessages() : array
	{
		return $this->messages->getMessages();
	}
}