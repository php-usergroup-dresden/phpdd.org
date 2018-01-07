<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Constants;

/**
 * Class TicketType
 * @package PHPUGDD\PHPDD\Website\Application\Constants
 */
abstract class TicketTypes
{
	public const WORKSHOP_SLOT_A = 'workshop-slot-a';

	public const WORKSHOP_SLOT_B = 'workshop-slot-b';

	public const CONFERENCE      = 'conference';

	public const ALL             = [
		self::WORKSHOP_SLOT_A,
		self::WORKSHOP_SLOT_B,
		self::CONFERENCE,
	];

	public const WORKSHOPS       = [
		self::WORKSHOP_SLOT_A,
		self::WORKSHOP_SLOT_B,
	];
}
