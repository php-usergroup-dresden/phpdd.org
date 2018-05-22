<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItemCollection;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class TicketItemCollectionTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;

	/** @var TicketItemCollection */
	private $collection;

	/** @var Ticket */
	private $conferenceTicket;

	/** @var Ticket */
	private $workshopTicket;

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	protected function setUp() : void
	{
		$this->collection       = new TicketItemCollection();
		$this->conferenceTicket = $this->getConferenceTicket( $this->getMoney( 11900 ) );
		$this->workshopTicket   = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_A,
			'Workshop Ticket Slot A',
			'Workshop description slot A',
			$this->getMoney( 25000 )
		);

		$johnDoe = new AttendeeName( 'John Doe' );
		$janeDoe = new AttendeeName( 'Jane Doe' );

		$conferenceTicketItemJohn = new TicketItem( $this->conferenceTicket, $johnDoe );
		$workshopTicketItemJohn   = new TicketItem( $this->workshopTicket, $johnDoe );

		$conferenceTicketItemJane = new TicketItem( $this->conferenceTicket, $janeDoe );
		$workshopTicketItemJane   = new TicketItem( $this->workshopTicket, $janeDoe );

		$this->collection->add( $conferenceTicketItemJohn );
		$this->collection->add( clone $conferenceTicketItemJohn );

		$this->collection->add( $conferenceTicketItemJane );

		$this->collection->add( $workshopTicketItemJohn );
		$this->collection->add( clone $workshopTicketItemJohn );
		$this->collection->add( clone $workshopTicketItemJohn );

		$this->collection->add( $workshopTicketItemJane );
		$this->collection->add( clone $workshopTicketItemJane );
	}

	protected function tearDown() : void
	{
		$this->collection = null;
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function testCanGetCountForTicket() : void
	{
		$otherWorkshopTicketA = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_A,
			'Other Workshop Ticket Slot A',
			'Other Workshop description Slot A',
			$this->getMoney( 25000 )
		);

		$workshopTicketB = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_B,
			'Workshop Ticket Slot B',
			'Workshop description Slot B',
			$this->getMoney( 25000 )
		);

		$this->assertSame( 3, $this->collection->getCountForTicket( $this->conferenceTicket ) );
		$this->assertSame( 5, $this->collection->getCountForTicket( $this->workshopTicket ) );
		$this->assertSame( 0, $this->collection->getCountForTicket( $otherWorkshopTicketA ) );
		$this->assertSame( 0, $this->collection->getCountForTicket( $workshopTicketB ) );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetCountForTypeAndAttendeeName() : void
	{
		$attendeeName = new AttendeeName( 'John Doe' );
		$conference   = new TicketType( TicketTypes::CONFERENCE );
		$workshopA    = new TicketType( TicketTypes::WORKSHOP_SLOT_A );
		$workshopB    = new TicketType( TicketTypes::WORKSHOP_SLOT_B );

		$this->assertSame(
			2,
			$this->collection->getCountForTypeAndAttendeeName(
				$conference,
				$attendeeName
			)
		);

		$this->assertSame(
			3,
			$this->collection->getCountForTypeAndAttendeeName(
				$workshopA,
				$attendeeName
			)
		);

		$this->assertSame(
			0,
			$this->collection->getCountForTypeAndAttendeeName(
				$workshopB,
				$attendeeName
			)
		);
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetCountForType() : void
	{
		$conference = new TicketType( TicketTypes::CONFERENCE );
		$workshopA  = new TicketType( TicketTypes::WORKSHOP_SLOT_A );
		$workshopB  = new TicketType( TicketTypes::WORKSHOP_SLOT_B );

		$this->assertSame( 3, $this->collection->getCountForType( $conference ) );
		$this->assertSame( 5, $this->collection->getCountForType( $workshopA ) );
		$this->assertSame( 0, $this->collection->getCountForType( $workshopB ) );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanIterateOverCollection() : void
	{
		$this->collection->rewind();
		while ( $this->collection->valid() )
		{
			/** @noinspection UnnecessaryAssertionInspection */
			$this->assertInstanceOf( TicketItem::class, $this->collection->current() );
			$this->assertGreaterThanOrEqual( 0, $this->collection->key() );
			$this->collection->next();
		}

		$this->assertSame( 8, $this->collection->count() );
	}
}
