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
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

/**
 * Class TicketConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Configs
 */
final class TicketConfig
{
	use MoneyProviding;

	/** @var string */
	private $id;

	/** @var string */
	private $name;

	/** @var string */
	private $description;

	/** @var int */
	private $price;

	/** @var int */
	private $seats;

	/** @var int */
	private $maxSeatsPerOrder;

	/** @var string */
	private $type;

	/** @var string */
	private $validFrom;

	/** @var string */
	private $validTo;

	/** @var string */
	private $image;

	public function __construct(
		string $id,
		string $name,
		string $description,
		int $price,
		int $seats,
		int $maxSeatsPerOrder,
		string $type,
		string $validFrom,
		string $validTo,
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
		return new TicketId( $this->id );
	}

	public function getName() : TicketName
	{
		return new TicketName( $this->name );
	}

	public function getDescription() : TicketDescription
	{
		return new TicketDescription( $this->description );
	}

	/**
	 * @return TicketPrice
	 * @throws \InvalidArgumentException
	 */
	public function getPrice() : TicketPrice
	{
		return new TicketPrice( $this->getMoney( $this->price ) );
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
		return new TicketType( $this->type );
	}

	/**
	 * @throws \Exception
	 * @return DateTimeImmutable
	 */
	public function getValidFrom() : DateTimeImmutable
	{
		return new DateTimeImmutable( $this->validFrom );
	}

	/**
	 * @throws \Exception
	 * @return DateTimeImmutable
	 */
	public function getValidTo() : DateTimeImmutable
	{
		return new DateTimeImmutable( $this->validTo );
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
