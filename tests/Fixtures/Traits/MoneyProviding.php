<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits;

use Money\Currency;
use Money\Money;

/**
 * Trait MoneyProviding
 * @package PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits
 */
trait MoneyProviding
{
	protected function getMoney( int $amount ) : Money
	{
		return new Money( $amount, new Currency( 'EUR' ) );
	}
}
