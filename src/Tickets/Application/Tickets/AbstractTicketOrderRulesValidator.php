<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ValidatesTicketAvailability;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ValidatesTicketOrderRules;

/**
 * Class AbstractTicketRuleValidator
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets
 */
abstract class AbstractTicketOrderRulesValidator implements ValidatesTicketOrderRules
{
	/** @var CollectsTicketItems */
	private $ticketItems;

	/** @var ValidatesTicketAvailability */
	private $availabilityValidator;

	public function __construct( TicketsConfig $ticketsConfig, ProvidesReservedTicketCount $reservedTicketCounts, CollectsTicketItems $ticketItems )
	{
		$this->ticketItems           = $ticketItems;
		$this->availabilityValidator = new TicketAvailabilityValidator( $ticketsConfig, $reservedTicketCounts, $ticketItems );
	}

	final protected function getTicketItems() : CollectsTicketItems
	{
		return $this->ticketItems;
	}

	final protected function getAvailabilityValidator() : ValidatesTicketAvailability
	{
		return $this->availabilityValidator;
	}
}
