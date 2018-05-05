<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderBillingAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\City;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\ZipCode;
use PHPUnit\Framework\TestCase;

final class TicketOrderBillingAddressTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetAddressAsString() : void
	{
		$address = new TicketOrderBillingAddress(
			new Firstname( 'John' ),
			new Lastname( 'Doe' ),
			new CompanyName( 'ACME Inc.' ),
			new StreetWithNumber( 'ACME Plaza 123' ),
			new AddressAddon( 'c/o Cats & Dogs' ),
			new ZipCode( '98765' ),
			new City( 'Uptown Hollywood' ),
			new CountryCode( CountryCodes::US_SHORT ),
			null
		);

		$expectedString = "ACME Inc.\nJohn Doe\nACME Plaza 123\nc/o Cats & Dogs\nUS-98765 Uptown Hollywood";

		$this->assertSame( $expectedString, $address->toString() );
	}
}
