<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

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
		foreach ( $this->configData as $ticketName => $ticketData )
		{
			yield new TicketConfig(
				(string)$ticketName,
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
	 * @param TicketType $ticketType
	 * @param TicketName $ticketName
	 *
	 * @return TicketConfig
	 * @throws TicketConfigNotFoundException
	 */
	public function findTicketConfigByTypeAndName( TicketType $ticketType, TicketName $ticketName ) : TicketConfig
	{
		if ( !isset( $this->configData[ $ticketName->toString() ] ) )
		{
			throw new TicketConfigNotFoundException(
				'Could not find ticket config with name: ' . $ticketName->toString()
			);
		}

		if ( $this->configData[ $ticketName->toString() ]['type'] !== $ticketType->toString() )
		{
			throw new TicketConfigNotFoundException(
				sprintf(
					'Could not find ticket config with name "%s" and type "%s".',
					$ticketName->toString(),
					$ticketType->toString()
				)
			);
		}

		$ticketData = $this->configData[ $ticketName->toString() ];

		return new TicketConfig(
			$ticketName->toString(),
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
