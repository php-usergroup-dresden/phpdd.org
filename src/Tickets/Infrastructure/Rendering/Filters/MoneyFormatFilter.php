<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters;

use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Exceptions\MissingCurrencyCodeException;
use function is_scalar;

/**
 * Class MoneyFormatFilter
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters
 */
final class MoneyFormatFilter
{
	/** @var MoneyFormatter */
	private $formatter;

	public function __construct( MoneyFormatter $formatter )
	{
		$this->formatter = $formatter;
	}

	/**
	 * @param int|Money   $moneyValue
	 * @param string|null $currencyCode
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 * @throws MissingCurrencyCodeException
	 */
	public function formatMoneyValue( $moneyValue, ?string $currencyCode = null ) : string
	{
		if ( $moneyValue instanceof Money )
		{
			return $this->formatter->format( $moneyValue );
		}

		if ( null !== $currencyCode && is_scalar( $moneyValue ) )
		{
			$money = new Money( (int)$moneyValue, new Currency( $currencyCode ) );

			return $this->formatter->format( $money );
		}

		throw new MissingCurrencyCodeException( 'Missing currency code' );
	}
}
