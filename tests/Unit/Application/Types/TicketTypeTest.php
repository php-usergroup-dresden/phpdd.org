<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketTypeTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class TicketTypeTest extends TestCase
{
	/**
	 * @param string $type
	 *
	 * @throws \PHPUnit\Framework\Exception
	 *
	 * @dataProvider validTicketTypesProvider
	 */
	public function testCanCreateInstanceForValidTicketTypes( string $type ) : void
	{
		$ticketType = new TicketType( $type );

		$this->assertInstanceOf( TicketType::class, $ticketType );
		$this->assertSame( $type, $ticketType->toString() );
	}

	public function validTicketTypesProvider() : array
	{
		return array_map(
			function ( string $type )
			{
				return ['type' => $type];
			},
			TicketTypes::ALL
		);
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionForInvalidTicketType() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketType( 'unconference' );
	}
}
