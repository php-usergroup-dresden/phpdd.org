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
				'code' => 'A1B2C3D4',
			],
			[
				'code' => 'AAAAAAA1',
			],
			[
				'code' => '1AAAAAAA',
			],
		];
	}

	/**
	 * @throws \Exception
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testCanGenerateADiscountCode() : void
	{
		$discountCode = DiscountCode::generate();

		$this->assertInstanceOf( DiscountCode::class, $discountCode );
	}

	/**
	 * @param string $code
	 * @param string $expectedExceptionMessage
	 *
	 * @dataProvider invalidDiscountCodeProvider
	 */
	public function testThrowsExceptionForInvalidCodes( string $code, string $expectedExceptionMessage ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( $expectedExceptionMessage );

		new DiscountCode( $code );
	}

	public function invalidDiscountCodeProvider() : array
	{
		return array_merge(
			[
				# Code is too short
				[
					'code'                     => 'A1234B',
					'expectedExceptionMessage' => 'Invalid discount code provided.',
				],
				# Code has no uppercase letters
				[
					'code'                     => 'a123456b',
					'expectedExceptionMessage' => 'Discount code has no uppercase letters.',
				],
				# Code has no digits
				[
					'code'                     => 'ABCDEFGH',
					'expectedExceptionMessage' => 'Discount code has no digits.',
				],
			]
		);
	}
}
