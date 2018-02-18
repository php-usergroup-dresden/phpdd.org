<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;

/**
 * Class TicketName
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class TicketName extends AbstractStringType
{
	protected function guardValueIsValid( string $value ) : void
	{
		// TODO: Implement guardValueIsValid() method.
	}
}
