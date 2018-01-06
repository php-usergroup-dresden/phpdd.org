<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketOrderBillingAddress;
use PHPUGDD\PHPDD\Website\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Application\Types\City;
use PHPUGDD\PHPDD\Website\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Application\Types\ZipCode;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderBillingAddressTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets
 */
final class TicketOrderBillingAddressTest extends TestCase
{
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
			new CountryCode( CountryCodes::US_SHORT )
		);

		$expectedString = "ACME Inc.\nJohn Doe\nACME Plaza 123\nc/o Cats & Dogs\nUS-98765 Uptown Hollywood";

		$this->assertSame( $expectedString, $address->toString() );
	}
}
