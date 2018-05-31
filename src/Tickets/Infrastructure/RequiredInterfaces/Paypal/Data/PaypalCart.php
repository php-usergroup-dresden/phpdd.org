<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data;

final class PaypalCart
{
	/** @var string */
	private $cartTotal;

	/** @var string */
	private $currencyCode;

	/** @var string */
	private $description;

	/** @var array|PaypalCartItem[] */
	private $cartItems;

	public function __construct( string $cartTotal, string $currencyCode, string $description )
	{
		$this->cartItems    = [];
		$this->cartTotal    = $cartTotal;
		$this->currencyCode = $currencyCode;
		$this->description  = $description;
	}

	public function addCartItem( PaypalCartItem $cartItem ) : void
	{
		$this->cartItems[] = $cartItem;
	}

	/**
	 * @return array|PaypalCartItem[]
	 */
	public function getCartItems() : array
	{
		return $this->cartItems;
	}

	public function getCartTotal() : string
	{
		return $this->cartTotal;
	}

	public function getCurrencyCode() : string
	{
		return $this->currencyCode;
	}

	public function getDescription() : string
	{
		return $this->description;
	}
}
