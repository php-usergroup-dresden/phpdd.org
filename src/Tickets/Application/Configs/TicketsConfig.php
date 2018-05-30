<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use DateTimeImmutable;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class TicketsConfig
{
	use MoneyProviding;

	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		return new self( (array)require __DIR__ . '/../../../../config/Tickets.php' );
	}

	/**
	 * @throws \Exception
	 * @return TicketConfig[]|Generator
	 */
	public function getTicketConfigs() : Generator
	{
		foreach ( $this->configData as $ticketId => $ticketData )
		{
			yield new TicketConfig(
				new TicketId( (string)$ticketId ),
				new TicketName( (string)$ticketData['name'] ),
				new TicketDescription( (string)$ticketData['description'] ),
				new TicketPrice( $this->getMoney( (int)$ticketData['price'] ) ),
				(int)$ticketData['seats'],
				(int)$ticketData['maxSeatsPerOrder'],
				new TicketType( (string)$ticketData['type'] ),
				new DateTimeImmutable( (string)$ticketData['validFrom'] ),
				new DateTimeImmutable( (string)$ticketData['validTo'] ),
				(string)$ticketData['image']
			);
		}
	}

	/**
	 * @param TicketId $ticketId
	 *
	 * @throws TicketConfigNotFoundException
	 * @throws InvalidArgumentException
	 * @throws \Exception
	 * @return TicketConfig
	 */
	public function findTicketById( TicketId $ticketId ) : TicketConfig
	{
		if ( !isset( $this->configData[ $ticketId->toString() ] ) )
		{
			throw new TicketConfigNotFoundException(
				'Could not find ticket config with ID: ' . $ticketId->toString()
			);
		}

		$ticketData = $this->configData[ $ticketId->toString() ];

		return new TicketConfig(
			$ticketId,
			new TicketName( (string)$ticketData['name'] ),
			new TicketDescription( (string)$ticketData['description'] ),
			new TicketPrice( $this->getMoney( (int)$ticketData['price'] ) ),
			(int)$ticketData['seats'],
			(int)$ticketData['maxSeatsPerOrder'],
			new TicketType( (string)$ticketData['type'] ),
			new DateTimeImmutable( (string)$ticketData['validFrom'] ),
			new DateTimeImmutable( (string)$ticketData['validTo'] ),
			(string)$ticketData['image']
		);
	}
}
