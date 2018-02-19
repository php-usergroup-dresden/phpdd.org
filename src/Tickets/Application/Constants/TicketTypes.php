<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Constants;

/**
 * Class TicketType
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Constants
 */
abstract class TicketTypes
{
	public const WORKSHOP_SLOT_A = 'workshop-slot-a';

	public const WORKSHOP_SLOT_B = 'workshop-slot-b';

	public const WORKSHOP_SLOT_C = 'workshop-slot-c';

	public const CONFERENCE      = 'conference';

	public const ALL             = [
		self::WORKSHOP_SLOT_A,
		self::WORKSHOP_SLOT_B,
		self::WORKSHOP_SLOT_C,
		self::CONFERENCE,
	];

	public const WORKSHOPS       = [
		self::WORKSHOP_SLOT_A,
		self::WORKSHOP_SLOT_B,
		self::WORKSHOP_SLOT_C,
	];
}
