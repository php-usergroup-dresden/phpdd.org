<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data\PaypalAddress;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data\PaypalCart;

final class PaypalAuthorizeRequest
{
	/** @var string */
	private $orderId;

	/** @var PaypalAddress */
	private $shippingAddress;

	/** @var PaypalAddress */
	private $billingAddress;

	/** @var PaypalCart */
	private $cart;

	public function __construct(
		string $orderId,
		PaypalAddress $shippingAddress,
		PaypalAddress $billingAddress,
		PaypalCart $cart
	)
	{
		$this->orderId         = $orderId;
		$this->shippingAddress = $shippingAddress;
		$this->billingAddress  = $billingAddress;
		$this->cart            = $cart;
	}

	public function getOrderId() : string
	{
		return $this->orderId;
	}

	public function getShippingAddress() : PaypalAddress
	{
		return $this->shippingAddress;
	}

	public function getBillingAddress() : PaypalAddress
	{
		return $this->billingAddress;
	}

	public function getCart() : PaypalCart
	{
		return $this->cart;
	}
}
