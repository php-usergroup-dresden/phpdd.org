<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class TicketTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 */
	public function testCanGetValues() : void
	{
		$ticket = new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Name of the ticket' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);

		$this->assertSame( TicketTypes::CONFERENCE, $ticket->getType()->toString() );
		$this->assertSame( 'Name of the ticket', $ticket->getName()->toString() );
		$this->assertSame( 'Description of the ticket', $ticket->getDescription()->toString() );
		$this->assertTrue( $this->getMoney( 12300 )->equals( $ticket->getPrice()->getMoney() ) );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 */
	public function testCanCheckIfTicketsAreEqual() : void
	{
		$ticket1 = new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Name of the ticket' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);
		$ticket2 = new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Name of the ticket' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);
		$ticket3 = new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Name of the ticket' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 32100 ) )
		);

		$this->assertTrue( $ticket1->equals( $ticket2 ) );
		$this->assertTrue( $ticket1->equals( $ticket3 ) );
		$this->assertTrue( $ticket2->equals( $ticket1 ) );
		$this->assertTrue( $ticket2->equals( $ticket3 ) );
		$this->assertTrue( $ticket3->equals( $ticket1 ) );
		$this->assertTrue( $ticket3->equals( $ticket2 ) );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 */
	public function testTicketsAreNotEqualIfTicketIdIsDifferent() : void
	{
		$ticket1 = new Ticket(
			new TicketId( 'PHPDD18-CT-01' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Conference-Ticket' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);
		$ticket2 = new Ticket(
			new TicketId( 'PHPDD18-CT-02' ),
			new TicketType( TicketTypes::CONFERENCE ),
			new TicketName( 'Ticket for Conference' ),
			new TicketDescription( 'Description of the ticket' ),
			new TicketPrice( $this->getMoney( 12300 ) )
		);

		$this->assertFalse( $ticket1->equals( $ticket2 ) );
		$this->assertFalse( $ticket2->equals( $ticket1 ) );
	}
}
