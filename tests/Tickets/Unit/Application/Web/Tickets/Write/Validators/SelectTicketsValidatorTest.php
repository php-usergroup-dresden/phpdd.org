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
			'PHPDD18-CT-01' => [
				'name'             => 'TicketNameA',
				'type'             => TicketTypes::CONFERENCE,
				'description'      => 'Ticket Description A',
				'image'            => '',
				'price'            => 11900,
				'seats'            => 10,
				'maxSeatsPerOrder' => 5,
				'validFrom'        => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '+1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			# Not available but selected ticket
			'PHPDD18-EB-01' => [
				'name'             => 'TicketNameB',
				'type'             => TicketTypes::CONFERENCE,
				'description'      => 'Ticket Description B',
				'image'            => '',
				'price'            => 7900,
				'seats'            => 10,
				'maxSeatsPerOrder' => 5,
				'validFrom'        => (new DateTimeImmutable( '-2 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			# Not available but selected ticket
			'PHPDD18-WS-01' => [
				'name'             => 'TicketNameC',
				'type'             => TicketTypes::WORKSHOP_SLOT_A,
				'description'      => 'Ticket Description C',
				'image'            => '',
				'price'            => 24900,
				'seats'            => 10,
				'maxSeatsPerOrder' => 3,
				'validFrom'        => (new DateTimeImmutable( '-2 day' ))->format( 'Y-m-d H:i:s' ),
				'validTo'          => (new DateTimeImmutable( '-1 day' ))->format( 'Y-m-d H:i:s' ),
			],
			'PHPDD18-WS-02' => [
				'name'             => 'TicketNameD',
				'type'             => TicketTypes::WORKSHOP_SLOT_A,
				'description'      => 'Ticket Description D',
				'image'            => '',
				'price'            => 24900,
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
	 * @throws \Exception
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
						'PHPDD18-WS-02' => 0,
						'PHPDD18-CT-01' => 0,
					],
				],
				'expectedMessages' => ['general' => ['Please select at least one ticket.']],
			],
			[
				'input'            => [
					'quantity' => [
						'PHPDD18-CT-01' => 0,
						'PHPDD18-EB-01' => 1,
					],
				],
				'expectedMessages' => ['general' => ['Please select only currently available tickets.']],
			],
			[
				'input'            => [
					'quantity' => [
						'PHPDD18-EB-01' => 1,
						'PHPDD18-WS-01' => 1,
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
	public function testValidationOfTicketSelectionPasses( array $input ) : void
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
						'PHPDD18-CT-01' => 1,
						'PHPDD18-WS-02' => 1,
					],
				],
			],
		];
	}
}
