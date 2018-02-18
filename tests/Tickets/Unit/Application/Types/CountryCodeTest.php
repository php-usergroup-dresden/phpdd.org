<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUnit\Framework\TestCase;

/**
 * Class CountryCodeTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class CountryCodeTest extends TestCase
{
	/**
	 * @param string $code
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider validCountryCodesProvider
	 */
	public function testCanCreateInstanceForValidCountryCodes( string $code ) : void
	{
		$countryCode = new CountryCode( $code );

		$this->assertInstanceOf( CountryCode::class, $countryCode );
	}

	public function validCountryCodesProvider() : array
	{
		return array_map(
			function ( string $code )
			{
				return ['code' => $code];
			},
			CountryCodes::ALL_SHORT
		);
	}

	/**
	 */
	public function testThrowsExceptionForInvalidCountryCode() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new CountryCode( 'XY' );
	}
}
