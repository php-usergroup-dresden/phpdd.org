<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\EmptyStringProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUnit\Framework\TestCase;

final class DiscountNameTest extends TestCase
{
	use EmptyStringProviding;

	/**
	 * @param string $value
	 *
	 * @dataProvider emptyStringProvider
	 */
	public function testThrowsExceptionWhenConstructedWithEmptyString( string $value ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Discount name cannot be empty.' );

		new DiscountName( $value );
	}
}
