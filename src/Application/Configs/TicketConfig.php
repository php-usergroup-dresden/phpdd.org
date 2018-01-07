<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Configs;

use PHPUGDD\PHPDD\Website\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Traits\MoneyProviding;

/**
 * Class TicketConfig
 * @package PHPUGDD\PHPDD\Website\Application\Configs
 */
final class TicketConfig
{
	use MoneyProviding;

	/** @var string */
	private $name;

	/** @var string */
	private $description;

	/** @var int */
	private $price;

	/** @var int */
	private $seats;

	/** @var string */
	private $type;

	/** @var string */
	private $validFrom;

	/** @var string */
	private $validTo;

	/** @var string */
	private $image;

	public function __construct( string $name, string $description, int $price, int $seats, string $type, string $validFrom, string $validTo, string $image )
	{
		$this->name        = $name;
		$this->description = $description;
		$this->price       = $price;
		$this->seats       = $seats;
		$this->type        = $type;
		$this->validFrom   = $validFrom;
		$this->validTo     = $validTo;
		$this->image       = $image;
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

	public function getType() : TicketType
	{
		return new TicketType( $this->type );
	}

	public function getValidFrom() : \DateTimeImmutable
	{
		return new \DateTimeImmutable( $this->validFrom );
	}

	public function getValidTo() : \DateTimeImmutable
	{
		return new \DateTimeImmutable( $this->validTo );
	}

	public function getImage() : string
	{
		return $this->image;
	}
}
