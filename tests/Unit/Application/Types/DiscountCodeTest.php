<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use PHPUGDD\PHPDD\Website\Application\Types\DiscountCode;
use PHPUnit\Framework\TestCase;

/**
 * Class DiscountCodeTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class DiscountCodeTest extends TestCase
{
	/**
	 * @param string $code
	 *
	 * @throws \PHPUnit\Framework\Exception
	 *
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
}
