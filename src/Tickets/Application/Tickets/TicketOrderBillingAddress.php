<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\City;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\VatNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\ZipCode;

/**
 * Class TicketOrderBillingAddress
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets
 */
final class TicketOrderBillingAddress
{
	/** @var Firstname */
	private $firstname;

	/** @var Lastname */
	private $lastname;

	/** @var null|CompanyName */
	private $companyName;

	/** @var StreetWithNumber */
	private $streetWithNumber;

	/** @var null|AddressAddon */
	private $addressAddon;

	/** @var ZipCode */
	private $zipCode;

	/** @var City */
	private $city;

	/** @var CountryCode */
	private $countryCode;

	/** @var null|VatNumber */
	private $vatNumber;

	public function __construct(
		Firstname $firstname,
		Lastname $lastname,
		?CompanyName $companyName,
		StreetWithNumber $streetWithNumber,
		?AddressAddon $addressAddon,
		ZipCode $zipCode,
		City $city,
		CountryCode $countryCode,
		?VatNumber $vatNumber
	)
	{
		$this->firstname        = $firstname;
		$this->lastname         = $lastname;
		$this->companyName      = $companyName;
		$this->streetWithNumber = $streetWithNumber;
		$this->addressAddon     = $addressAddon;
		$this->zipCode          = $zipCode;
		$this->city             = $city;
		$this->countryCode      = $countryCode;
		$this->vatNumber        = $vatNumber;
	}

	public function getFirstname() : Firstname
	{
		return $this->firstname;
	}

	public function getLastname() : Lastname
	{
		return $this->lastname;
	}

	public function getCompanyName() : ?CompanyName
	{
		return $this->companyName;
	}

	public function getStreetWithNumber() : StreetWithNumber
	{
		return $this->streetWithNumber;
	}

	public function getAddressAddon() : ?AddressAddon
	{
		return $this->addressAddon;
	}

	public function getZipCode() : ZipCode
	{
		return $this->zipCode;
	}

	public function getCity() : City
	{
		return $this->city;
	}

	public function getCountryCode() : CountryCode
	{
		return $this->countryCode;
	}

	public function getVatNumber() : ?VatNumber
	{
		return $this->vatNumber;
	}

	public function toString() : string
	{
		return sprintf(
			"%s\n%s %s\n%s\n%s\n%s-%s %s",
			$this->companyName->toString(),
			$this->firstname->toString(),
			$this->lastname->toString(),
			$this->streetWithNumber->toString(),
			$this->addressAddon->toString(),
			$this->countryCode->toString(),
			$this->zipCode->toString(),
			$this->city->toString()
		);
	}
}
