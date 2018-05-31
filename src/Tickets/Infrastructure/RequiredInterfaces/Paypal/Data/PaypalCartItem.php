<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data;

final class PaypalCartItem
{
	/** @var string */
	private $name;

	/** @var string */
	private $description;

	/** @var int */
	private $quantity;

	/** @var string */
	private $price;

	/** @var string */
	private $currencyCode;

	public function __construct( string $name, string $description, int $quantity, string $price, string $currencyCode )
	{
		$this->name         = $name;
		$this->description  = $description;
		$this->quantity     = $quantity;
		$this->price        = $price;
		$this->currencyCode = $currencyCode;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getQuantity() : int
	{
		return $this->quantity;
	}

	public function getPrice() : string
	{
		return $this->price;
	}

	public function getCurrencyCode() : string
	{
		return $this->currencyCode;
	}
}
