<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Configs;

/**
 * Class TicketsConfig
 * @package PHPUGDD\PHPDD\Website\Application\Configs
 */
final class TicketsConfig
{
	/** @var array */
	private $configData;

	public function __construct()
	{
		$this->configData = require __DIR__ . '/../../../config/Tickets.php';
	}

	public function getTicketConfigs() : \Generator
	{
		foreach ( (array)$this->configData as $ticketName => $ticketData )
		{
			yield new TicketConfig(
				(string)$ticketName,
				(string)$ticketData['description'],
				(int)$ticketData['price'],
				(int)$ticketData['seats'],
				(string)$ticketData['type'],
				(string)$ticketData['validFrom'],
				(string)$ticketData['validTo'],
				(string)$ticketData['image']
			);
		}
	}
}
