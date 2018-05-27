<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;

final class TicketsConfig
{
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
	 * @return TicketConfig[]|Generator
	 */
	public function getTicketConfigs() : Generator
	{
		foreach ( $this->configData as $ticketId => $ticketData )
		{
			yield new TicketConfig(
				(string)$ticketId,
				(string)$ticketData['name'],
				(string)$ticketData['description'],
				(int)$ticketData['price'],
				(int)$ticketData['seats'],
				(int)$ticketData['maxSeatsPerOrder'],
				(string)$ticketData['type'],
				(string)$ticketData['validFrom'],
				(string)$ticketData['validTo'],
				(string)$ticketData['image']
			);
		}
	}

	/**
	 * @param TicketId $ticketId
	 *
	 * @throws TicketConfigNotFoundException
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
			$ticketId->toString(),
			(string)$ticketData['name'],
			(string)$ticketData['description'],
			(int)$ticketData['price'],
			(int)$ticketData['seats'],
			(int)$ticketData['maxSeatsPerOrder'],
			(string)$ticketData['type'],
			(string)$ticketData['validFrom'],
			(string)$ticketData['validTo'],
			(string)$ticketData['image']
		);
	}
}
