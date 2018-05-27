<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesSelectedTicketInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

final class SelectedTicketInfo implements ProvidesSelectedTicketInformation
{
	/** @var TicketConfig */
	private $ticketConfig;

	/** @var int */
	private $quantity;

	public function __construct( TicketConfig $ticketConfig, int $quantity )
	{
		$this->ticketConfig = $ticketConfig;
		$this->quantity     = $quantity;
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

	/**
	 * @throws \InvalidArgumentException
	 * @return TicketPrice
	 */
	public function getPrice() : TicketPrice
	{
		return $this->ticketConfig->getPrice();
	}

	public function getQuantity() : int
	{
		return $this->quantity;
	}

}