<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Traits;

use Money\Currency;
use Money\Money;

/**
 * Trait MoneyProviding
 * @package PHPUGDD\PHPDD\Website\Tickets\Traits
 */
trait MoneyProviding
{
	/**
	 * @param int $amount
	 *
	 * @return Money
	 * @throws \InvalidArgumentException
	 */
	protected function getMoney( int $amount ) : Money
	{
		return new Money( $amount, new Currency( 'EUR' ) );
	}
}