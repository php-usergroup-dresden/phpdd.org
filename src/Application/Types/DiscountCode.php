<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Fortuneglobe\Types\AbstractStringType;

/**
 * Class DiscountCode
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class DiscountCode extends AbstractStringType
{
	protected function guardValueIsValid( string $value ) : void
	{
	}
}
