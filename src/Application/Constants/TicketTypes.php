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
	public const WORKSHOP   = 'workshop';

	public const CONFERENCE = 'conference';

	public const ALL        = [
		self::WORKSHOP,
		self::CONFERENCE,
	];
}
