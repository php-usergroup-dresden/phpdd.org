<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\EmptyStringProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUnit\Framework\TestCase;

/**
 * Class DiscountCodeTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class DiscountCodeTest extends TestCase
{
	use EmptyStringProviding;

	/**
	 * @param string $code
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider validDiscountCodeProvider
	 */
	public function testCanCreateInstanceFromValidCodes( string $code ) : void
	{
		$discountCode = new DiscountCode( $code );

		$this->assertInstanceOf( DiscountCode::class, $discountCode );
		$this->assertSame( $code, $discountCode->toString() );
	}

	public function validDiscountCodeProvider() : array
	{
		return [
			[
				'code' => 'D87318324E',
			],
			[
				'code' => 'P95318357E',
			],
		];
	}

	/**
	 * @param string $code
	 *
	 * @dataProvider invalidDiscountCodeProvider
	 */
	public function testThrowsExceptionForInvalidCodes( string $code ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid discount code provided.' );

		new DiscountCode( $code );
	}

	public function invalidDiscountCodeProvider() : array
	{
		return array_merge(
			[
				# Code is too short
				[
					'code' => 'A1234B',
				],
				# Code has no uppercase letters
				[
					'code' => 'a12318456b',
				],
				# Code has no digits
				[
					'code' => 'ABCDEFGHIJ',
				],
			]
		);
	}
}
