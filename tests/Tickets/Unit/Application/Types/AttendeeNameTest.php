<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\EmptyStringProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUnit\Framework\TestCase;

final class AttendeeNameTest extends TestCase
{
	use EmptyStringProviding;

	/**
	 * @param string $name
	 *
	 * @dataProvider emptyStringProvider
	 */
	public function testEmptyAttendeeNameThrowException( string $name ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Attendee name cannot be empty.' );

		new AttendeeName( $name );
	}
}
