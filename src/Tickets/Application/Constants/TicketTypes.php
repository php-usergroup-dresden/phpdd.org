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
	public const FULLDAY_WORKSHOP   = 'fullday-workshop';

	public const HALFDAY_WORKSHOP_A = 'halfday-workshop-a';

	public const HALFDAY_WORKSHOP_B = 'halfday-workshop-b';

	public const CONFERENCE         = 'conference';

	public const ALL                = [
		self::FULLDAY_WORKSHOP,
		self::HALFDAY_WORKSHOP_A,
		self::HALFDAY_WORKSHOP_B,
		self::CONFERENCE,
	];

	public const ALL_WORKSHOPS      = [
		self::FULLDAY_WORKSHOP,
		self::HALFDAY_WORKSHOP_A,
		self::HALFDAY_WORKSHOP_B,
	];

	public const HALFDAY_WORKSHOPS  = [
		self::HALFDAY_WORKSHOP_A,
		self::HALFDAY_WORKSHOP_B,
	];
}
