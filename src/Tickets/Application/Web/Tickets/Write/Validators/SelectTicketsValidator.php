<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Generator;
use hollodotme\FluidValidator\MessageCollectors\GroupedListMessageCollector;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use function array_keys;
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
	 * @throws \Exception
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
	 * @throws \Exception
	 * @return bool
	 */
	public function passed() : bool
	{
		return !$this->failed();
	}

	private function noTicketWasSelected( array $quantities ) : bool
	{
		$countSelected = 0;
		foreach ( $quantities as $id => $quantity )
		{
			$countSelected += (int)$quantity;
		}

		return $countSelected === 0;
	}

	/**
	 * @param array $quantities
	 *
	 * @throws \Exception
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
			/** @noinspection PhpRedundantCatchClauseInspection */
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
	 * @return TicketConfig[]|Generator
	 */
	private function getTicketConfigs( array $quantities ) : Generator
	{
		foreach ( array_keys( $quantities ) as $id )
		{
			$ticketId = new TicketId( (string)$id );

			yield $this->ticketsConfig->findTicketById( $ticketId );
		}
	}

	public function getMessages() : array
	{
		return $this->messages->getMessages();
	}
}