<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderEmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderEmailAddressTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class TicketOrderEmailAddressTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testCanCreateInstanceForValidEmailAddresses() : void
	{
		$email = new TicketOrderEmailAddress( 'you@example.com' );

		$this->assertInstanceOf( TicketOrderEmailAddress::class, $email );
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionForInvalidEmailAddress() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketOrderEmailAddress( 'you @example.com' );
	}
}
