<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ValidatesTicketAvailability;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketAvailabilityValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItemCollection;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class TicketAvailabilityValidatorTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;

	/** @var ValidatesTicketAvailability */
	private $validator;

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	protected function setUp() : void
	{
		$ticketsConfig           = new TicketsConfig( (array)require __DIR__ . '/_files/TicketsConfig.php' );
		$reservedTicketsProvider = $this->getMockBuilder( ProvidesReservedTicketCount::class )
		                                ->setMethods( ['getReservedCount'] )
		                                ->getMockForAbstractClass();
		$reservedTicketsProvider->method( 'getReservedCount' )->willReturn( 2 );

		$johnDoe = new AttendeeName( 'John Doe' );
		$janeDoe = new AttendeeName( 'Jane Doe' );

		$ticketItemCollection = new TicketItemCollection();
		$ticketItemCollection->add(
			new TicketItem(
				$this->getConferenceTicket( $this->getMoney( 11900 ) ),
				$johnDoe
			)
		);
		$ticketItemCollection->add(
			new TicketItem(
				$this->getConferenceTicket( $this->getMoney( 11900 ) ),
				$janeDoe
			)
		);
		$ticketItemCollection->add(
			new TicketItem(
				$this->getWorkshopTicket(
					TicketTypes::HALFDAY_WORKSHOP_B,
					'Workshop Ticket Slot B2',
					'Half-day workshop from 02:30pm to 07:00pm on September 21st 2018',
					$this->getMoney( 14900 )
				),
				$janeDoe
			)
		);

		/** @var ProvidesReservedTicketCount $reservedTicketsProvider */

		$this->validator = new TicketAvailabilityValidator(
			$ticketsConfig,
			$reservedTicketsProvider,
			$ticketItemCollection
		);
	}

	protected function tearDown() : void
	{
		$this->validator = null;
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testReturnsFaleIfTicketConfigWasNotFound() : void
	{
		$ticket = new Ticket(
			new TicketId( 'PHPDD18-WS-07' ),
			new TicketType( TicketTypes::FULLDAY_WORKSHOP ),
			new TicketName( 'Ticket name' ),
			new TicketDescription( 'Ticket description' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);

		$johnDoe    = new AttendeeName( 'John Doe' );
		$ticketItem = new TicketItem( $ticket, $johnDoe );

		$this->assertFalse( $this->validator->isAvailable( $ticketItem ) );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanCheckIfTicketsAreAvailable() : void
	{
		$conferenceTicket     = $this->getConferenceTicket( $this->getMoney( 12300 ) );
		$johnDoe              = new AttendeeName( 'John Doe' );
		$conferenceTicketItem = new TicketItem( $conferenceTicket, $johnDoe );

		$this->assertTrue( $this->validator->isAvailable( $conferenceTicketItem ) );

		$workshopCTicket     = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_A,
			'Workshop Ticket Slot C1',
			'Full-day workshop from 09:00am to 07:00pm on September 21st 2018',
			$this->getMoney( 24900 )
		);
		$johnDoe             = new AttendeeName( 'John Doe' );
		$workshopCTicketItem = new TicketItem( $workshopCTicket, $johnDoe );

		$this->assertTrue( $this->validator->isAvailable( $workshopCTicketItem ) );

		$workshopBTicket     = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_B,
			'Workshop Ticket Slot B2',
			'Half-day workshop from 02:30pm to 07:00pm on September 21st 2018',
			$this->getMoney( 14900 )
		);
		$johnDoe             = new AttendeeName( 'John Doe' );
		$workshopBTicketItem = new TicketItem( $workshopBTicket, $johnDoe );

		$this->assertTrue( $this->validator->isAvailable( $workshopBTicketItem ) );
	}
}
