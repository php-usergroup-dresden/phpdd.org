<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\TicketOrderRules;

use PHPUGDD\PHPDD\Website\Application\Tickets\AbstractTicketOrderRulesValidator;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\ProvidesTicketItemInformation;

/**
 * Class TicketOrderRulesPHPDD18
 * @package PHPUGDD\PHPDD\Website\Application\TicketOrderRules
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
