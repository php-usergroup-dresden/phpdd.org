<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\TicketOrderRules;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\AbstractTicketOrderRulesValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketItemInformation;

/**
 * Class TicketOrderRulesPHPDD18
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\TicketOrderRules
 */
final class TicketOrderRulesPHPDD18 extends AbstractTicketOrderRulesValidator
{
	public function canOrderTicket( ProvidesTicketItemInformation $ticketItem ) : bool
	{
		if ( !$this->getAvailabilityValidator()->isAvailable( $ticketItem ) )
		{
			return false;
		}

		return true;
	}
}
