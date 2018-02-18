<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;

/**
 * Class CompanyName
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class CompanyName extends AbstractStringType
{
	protected function guardValueIsValid( string $value ) : void
	{
		// TODO: Implement guardValueIsValid() method.
	}
}