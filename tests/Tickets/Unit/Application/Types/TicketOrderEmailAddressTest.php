<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderEmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderEmailAddressTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class TicketOrderEmailAddressTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanCreateInstanceForValidEmailAddresses() : void
	{
		$email = new TicketOrderEmailAddress( 'you@example.com' );

		$this->assertInstanceOf( TicketOrderEmailAddress::class, $email );
	}

	public function testThrowsExceptionForInvalidEmailAddress() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid e-mail address provided: you @example.com' );

		new TicketOrderEmailAddress( 'you @example.com' );
	}
}
