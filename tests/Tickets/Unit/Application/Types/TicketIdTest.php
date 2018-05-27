<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\EmptyStringProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUnit\Framework\TestCase;

final class TicketIdTest extends TestCase
{
	use EmptyStringProviding;

	/**
	 * @param string $value
	 *
	 * @dataProvider invalidTicketIdProvider
	 */
	public function testThrowsExceptionWhenConstructedWithInvalidTicketId( string $value ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid ticket ID.' );

		new TicketId( $value );
	}

	public function invalidTicketIdProvider()
	{
		yield from $this->emptyStringProvider();

		# Wrong year
		yield ['value' => 'PHPDD17-WS-01'];

		# Wrong type
		yield ['value' => 'PHPDD18-TK-01'];

		# Wrong format
		yield ['value' => 'PHPDD18-WS-AB'];
	}
}
