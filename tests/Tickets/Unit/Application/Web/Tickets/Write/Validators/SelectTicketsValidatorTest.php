<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\SelectTicketsValidator;
use PHPUnit\Framework\TestCase;

final class SelectTicketsValidatorTest extends TestCase
{
	/** @var TicketsConfig */
	private $ticketsConfig;

	/**
	 * @throws \Exception
	 */
	protected function setUp()
	{
		$ticketConfig = [
			'TicketNameA' => [
				'type'             => TicketTypes::CONFERENCE,
				'description'      => '',
				'image'            => '',
				'price'            => 0,
				'seats'            => 10,
				'maxSeatsPerOrder' => 5,
				'validFrom'        => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '+1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			# Not available but not selected ticket
			'TicketNameB' => [
				'type'             => TicketTypes::CONFERENCE,
				'description'      => '',
				'image'            => '',
				'price'            => 0,
				'seats'            => 10,
				'maxSeatsPerOrder' => 5,
				'validFrom'        => (new DateTimeImmutable( '-2 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			# Not available but not selected ticket
			'TicketNameC' => [
				'type'             => TicketTypes::WORKSHOP_SLOT_A,
				'description'      => '',
				'image'            => '',
				'price'            => 0,
				'seats'            => 10,
				'maxSeatsPerOrder' => 3,
				'validFrom'        => (new DateTimeImmutable( '-2 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			'TicketNameD' => [
				'type'             => TicketTypes::WORKSHOP_SLOT_A,
				'description'      => '',
				'image'            => '',
				'price'            => 0,
				'seats'            => 10,
				'maxSeatsPerOrder' => 3,
				'validFrom'        => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '+1 day' ))->format( 'Y-m-d H:i:s' ),
			],
		];

		$this->ticketsConfig = new TicketsConfig( $ticketConfig );
	}

	protected function tearDown() : void
	{
		$this->ticketsConfig = null;
	}

	/**
	 * @param array $input
	 * @param array $expectedMessages
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider invalidTicketSelectionInputProvider
	 */
	public function testValidationOfTicketSelectionFails( array $input, array $expectedMessages ) : void
	{
		$userInput = new UserInput( $input );
		$validator = new SelectTicketsValidator( $userInput, $this->ticketsConfig );

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	public function invalidTicketSelectionInputProvider() : array
	{
		return [
			[
				'input'            => [],
				'expectedMessages' => ['general' => ['Please select at least one ticket.']],
			],
			[
				'input'            => [
					'quantity' => 'not-an-array',
				],
				'expectedMessages' => ['general' => ['Please select at least one ticket.']],
			],
			[
				'input'            => [
					'quantity' => [
						TicketTypes::CONFERENCE      => [
							'TicketNameA' => 0,
							'TicketNameB' => 0,
						],
						TicketTypes::WORKSHOP_SLOT_A => [
							'TicketNameC' => 0,
							'TicketNameD' => 0,
						],
					],
				],
				'expectedMessages' => ['general' => ['Please select at least one ticket.']],
			],
			[
				'input'            => [
					'quantity' => [
						TicketTypes::CONFERENCE      => [
							'TicketNameA' => 0,
							'TicketNameB' => 1,
						],
						TicketTypes::WORKSHOP_SLOT_A => [
							'TicketNameC' => 1,
							'TicketNameD' => 0,
						],
					],
				],
				'expectedMessages' => ['general' => ['Please select only currently available tickets.']],
			],
			[
				'input'            => [
					'quantity' => [
						'TicketTypeA' => [
							'TicketNameA' => 1,
							'TicketNameB' => 1,
						],
					],
				],
				'expectedMessages' => ['general' => ['Please select only currently available tickets.']],
			],
		];
	}

	/**
	 * @param array $input
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @throws \Exception
	 *
	 * @dataProvider validTicketSelectionInputProvider
	 */
	public function testValidationTicketSelectionPasses( array $input ) : void
	{
		$userInput = new UserInput( $input );

		$validator = new SelectTicketsValidator( $userInput, $this->ticketsConfig );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );
	}

	public function validTicketSelectionInputProvider() : array
	{
		return [
			[
				'input' => [
					'quantity' => [
						TicketTypes::CONFERENCE      => [
							'TicketNameA' => 1,
							'TicketNameB' => 0,
						],
						TicketTypes::WORKSHOP_SLOT_A => [
							'TicketNameC' => 0,
							'TicketNameD' => 3,
						],
					],
				],
			],
		];
	}
}
