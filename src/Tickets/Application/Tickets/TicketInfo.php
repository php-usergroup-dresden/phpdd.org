<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use function min;

final class TicketInfo implements ProvidesTicketInformation
{
	/** @var TicketConfig */
	private $ticketConfig;

	/** @var int */
	private $availableSeats;

	/** @var bool */
	private $soldOut;

	public function __construct( TicketConfig $ticketConfig, int $reservedSeats )
	{
		$this->ticketConfig   = $ticketConfig;
		$this->availableSeats = max( 0, $ticketConfig->getSeats() - $reservedSeats );
		$this->soldOut        = ($ticketConfig->getSeats() === $reservedSeats);
	}

	public function getId() : TicketId
	{
		return $this->ticketConfig->getId();
	}

	public function getType() : TicketType
	{
		return $this->ticketConfig->getType();
	}

	public function getName() : TicketName
	{
		return $this->ticketConfig->getName();
	}

	public function getDescription() : TicketDescription
	{
		return $this->ticketConfig->getDescription();
	}

	public function getImage() : string
	{
		return $this->ticketConfig->getImage();
	}

	public function getPrice() : TicketPrice
	{
		return $this->ticketConfig->getPrice();
	}

	public function getAvailableSeats() : int
	{
		return $this->availableSeats;
	}

	public function getMaxSeatsPerOrder() : int
	{
		return min( $this->getAvailableSeats(), $this->ticketConfig->getMaxSeatsPerOrder() );
	}

	/**
	 * @throws \Exception
	 * @return DateTimeImmutable
	 */
	public function getValidFrom() : DateTimeImmutable
	{
		return $this->ticketConfig->getValidFrom();
	}

	/**
	 * @throws \Exception
	 * @return DateTimeImmutable
	 */
	public function getValidTo() : DateTimeImmutable
	{
		return $this->ticketConfig->getValidTo();
	}

	/**
	 * @throws \Exception
	 * @return bool
	 */
	public function isAvailable() : bool
	{
		if ( !$this->ticketConfig->isAvailable() )
		{
			return false;
		}

		if ( 0 === $this->availableSeats )
		{
			return false;
		}

		return true;
	}

	public function isSoldOut() : bool
	{
		return $this->soldOut;
	}
}