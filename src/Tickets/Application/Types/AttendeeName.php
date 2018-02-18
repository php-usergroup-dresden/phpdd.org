<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;

/**
 * Class AttendeeName
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class AttendeeName extends AbstractStringType
{
	protected function guardValueIsValid( string $value ) : void
	{
		// TODO: Implement guardValueIsValid() method.
	}
}