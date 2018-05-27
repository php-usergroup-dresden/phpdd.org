<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

final class Ticket
{
	/** @var TicketId */
	private $id;

	/** @var TicketType */
	private $type;

	/** @var TicketName */
	private $name;

	/** @var TicketDescription */
	private $description;

	/** @var TicketPrice */
	private $price;

	public function __construct(
		TicketId $id,
		TicketType $type,
		TicketName $name,
		TicketDescription $description,
		TicketPrice $price
	)
	{
		$this->id          = $id;
		$this->type        = $type;
		$this->name        = $name;
		$this->description = $description;
		$this->price       = $price;
	}

	public function getId() : TicketId
	{
		return $this->id;
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

	public function equals( Ticket $other ) : bool
	{
		return $this->id->equals( $other->getId() );
	}
}
