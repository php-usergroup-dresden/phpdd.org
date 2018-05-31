<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data;

final class PaypalAddress
{
	/** @var string */
	private $firstname;

	/** @var string */
	private $lastname;

	/** @var string */
	private $addon;

	/** @var string */
	private $streetWithNumber;

	/** @var string */
	private $zipCode;

	/** @var string */
	private $city;

	/** @var string */
	private $countryCode;

	public function __construct(
		string $firstname,
		string $lastname,
		string $addon,
		string $streetWithNumber,
		string $zipCode,
		string $city,
		string $countryCode
	)
	{
		$this->firstname        = $firstname;
		$this->lastname         = $lastname;
		$this->addon            = $addon;
		$this->streetWithNumber = $streetWithNumber;
		$this->zipCode          = $zipCode;
		$this->city             = $city;
		$this->countryCode      = $countryCode;
	}

	public function getFirstname() : string
	{
		return $this->firstname;
	}

	public function getLastname() : string
	{
		return $this->lastname;
	}

	public function getAddon() : string
	{
		return $this->addon;
	}

	public function getStreetWithNumber() : string
	{
		return $this->streetWithNumber;
	}

	public function getZipCode() : string
	{
		return $this->zipCode;
	}

	public function getCity() : string
	{
		return $this->city;
	}

	public function getCountryCode() : string
	{
		return $this->countryCode;
	}
}
