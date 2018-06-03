<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Traits;

use InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

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
	 * @throws InvalidArgumentException
	 */
	protected function getMoney( int $amount ) : Money
	{
		return new Money( $amount, new Currency( 'EUR' ) );
	}

	/**
	 * @param int $amount
	 *
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function getDecimalFormattedMoney( int $amount ) : string
	{
		$formatter = new DecimalMoneyFormatter( new ISOCurrencies() );

		return $formatter->format( $this->getMoney( $amount ) );
	}

	/**
	 * @param int $amount
	 *
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function getFormattedMoney( int $amount ) : string
	{
		$numberFormatter = new NumberFormatter( 'en_GB', NumberFormatter::CURRENCY );
		$formatter       = new IntlMoneyFormatter( $numberFormatter, new ISOCurrencies() );

		return $formatter->format( $this->getMoney( $amount ) );
	}
}
