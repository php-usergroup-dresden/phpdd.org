<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe;

final class StripeExecuteRequest
{
	/** @var string */
	private $token;

	/** @var string */
	private $description;

	/** @var string */
	private $amount;

	/** @var string */
	private $currencyCode;

	public function __construct( string $token, string $description, string $amount, string $currencyCode )
	{
		$this->token        = $token;
		$this->description  = $description;
		$this->amount       = $amount;
		$this->currencyCode = $currencyCode;
	}

	public function getToken() : string
	{
		return $this->token;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getAmount() : string
	{
		return $this->amount;
	}

	public function getCurrencyCode() : string
	{
		return $this->currencyCode;
	}
}