<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

/**
 * Class TicketConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Configs
 */
final class TicketConfig
{
	/** @var TicketId */
	private $id;

	/** @var TicketName */
	private $name;

	/** @var TicketDescription */
	private $description;

	/** @var TicketPrice */
	private $price;

	/** @var int */
	private $seats;

	/** @var int */
	private $maxSeatsPerOrder;

	/** @var TicketType */
	private $type;

	/** @var DateTimeImmutable */
	private $validFrom;

	/** @var DateTimeImmutable */
	private $validTo;

	/** @var string */
	private $image;

	public function __construct(
		TicketId $id,
		TicketName $name,
		TicketDescription $description,
		TicketPrice $price,
		int $seats,
		int $maxSeatsPerOrder,
		TicketType $type,
		DateTimeImmutable $validFrom,
		DateTimeImmutable $validTo,
		string $image
	)
	{
		$this->id               = $id;
		$this->name             = $name;
		$this->description      = $description;
		$this->price            = $price;
		$this->seats            = $seats;
		$this->maxSeatsPerOrder = $maxSeatsPerOrder;
		$this->type             = $type;
		$this->validFrom        = $validFrom;
		$this->validTo          = $validTo;
		$this->image            = $image;
	}

	public function getId() : TicketId
	{
		return $this->id;
	}

	public function getName() : TicketName
	{
		return $this->name;
	}

	public function getDescription() : TicketDescription
	{
		return $this->description;
	}

	/**
	 * @return TicketPrice
	 */
	public function getPrice() : TicketPrice
	{
		return $this->price;
	}

	public function getSeats() : int
	{
		return $this->seats;
	}

	public function getMaxSeatsPerOrder() : int
	{
		return $this->maxSeatsPerOrder;
	}

	public function getType() : TicketType
	{
		return $this->type;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getValidFrom() : DateTimeImmutable
	{
		return $this->validFrom;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getValidTo() : DateTimeImmutable
	{
		return $this->validTo;
	}

	public function getImage() : string
	{
		return $this->image;
	}

	/**
	 * @throws \Exception
	 * @return bool
	 */
	public function isAvailable() : bool
	{
		$now = new DateTimeImmutable();

		return $this->getValidFrom() < $now && $now < $this->getValidTo();
	}
}
