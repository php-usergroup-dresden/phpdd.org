<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Configs;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUnit\Framework\TestCase;

final class TicketsConfigTest extends TestCase
{
	/** @var TicketsConfig */
	private $ticketsConfig;

	protected function setUp() : void
	{
		$configData = [
			'PHPDD18-EB-01' => [
				'name'             => 'Early Bird Conference Ticket',
				'description'      => 'Grants access to all conference activity on September 22, 2018.',
				'price'            => 7900,
				'seats'            => 50,
				'maxSeatsPerOrder' => 5,
				'type'             => \PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes::CONFERENCE,
				'validFrom'        => '2018-05-01 00:00:00',
				'validTo'          => '2018-09-15 23:59:59',
				'image'            => '/assets/images/tickets/early-bird.png',
			],
			'PHPDD18-CT-01' => [
				'name'             => 'Conference Ticket',
				'description'      => 'Grants access to all conference activity on September 22, 2018.',
				'price'            => 11900,
				'seats'            => 400,
				'maxSeatsPerOrder' => 10,
				'type'             => \PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes::CONFERENCE,
				'validFrom'        => '2018-05-01 00:00:00',
				'validTo'          => '2018-09-15 23:59:59',
				'image'            => '/assets/images/tickets/conference.png',
			],
		];

		$this->ticketsConfig = new TicketsConfig( $configData );
	}

	protected function tearDown() : void
	{
		$this->ticketsConfig = null;
	}

	/**
	 * @param string $ticketId
	 * @param string $expectedTicketNanme
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider ticketIdProvider
	 */
	public function testCanFindTicketById( string $ticketId, string $expectedTicketNanme ) : void
	{
		$ticketConfig = $this->ticketsConfig->findTicketById( new TicketId( $ticketId ) );

		$this->assertSame( $expectedTicketNanme, $ticketConfig->getName()->toString() );
	}

	public function ticketIdProvider() : array
	{
		return [
			[
				'ticketId'           => 'PHPDD18-EB-01',
				'expectedTicketName' => 'Early Bird Conference Ticket',
			],
			[
				'ticketId'           => 'PHPDD18-CT-01',
				'expectedTicketName' => 'Conference Ticket',
			],
		];
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetTicketConfigs() : void
	{
		$ticketConfigs = $this->ticketsConfig->getTicketConfigs();

		$this->assertInstanceOf( Generator::class, $ticketConfigs );
		$this->assertContainsOnlyInstancesOf( TicketConfig::class, $ticketConfigs );
	}
}
