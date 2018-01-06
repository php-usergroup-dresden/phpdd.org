<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;

/**
 * Class Ticket
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class Ticket
{
	/** @var TicketType */
	private $type;

	/** @var TicketName */
	private $name;

	/** @var TicketDescription */
	private $description;

	/** @var TicketPrice */
	private $price;

	public function __construct( TicketType $type, TicketName $name, TicketDescription $description, TicketPrice $price )
	{
		$this->type        = $type;
		$this->name        = $name;
		$this->description = $description;
		$this->price       = $price;
	}

	public function getType() : TicketType
	{
		return $this->type;
	}

	public function getName() : TicketName
	{
		return $this->name;
	}

	public function getDescription() : TicketDescription
	{
		return $this->description;
	}

	public function getPrice() : TicketPrice
	{
		return $this->price;
	}
}
