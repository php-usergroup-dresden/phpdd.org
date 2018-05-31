<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\CalculatesPaymentFee;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class StripeFeeCalculator implements CalculatesPaymentFee
{
	use MoneyProviding;

	private const VARIABLE_FEE_GERMANY         = 1.4;

	private const VARIABLE_FEE_OTHER_COUNTRIES = 2.9;

	private const FIXED_FEE                    = 25;

	/**
	 * @param Money       $total
	 * @param CountryCode $countryCode
	 *
	 * @throws \InvalidArgumentException
	 * @return Money
	 */
	public function getPaymentFee( Money $total, CountryCode $countryCode ) : Money
	{
		$vairableFee = self::VARIABLE_FEE_GERMANY;
		if ( !$countryCode->equals( new CountryCode( CountryCodes::DE_SHORT ) ) )
		{
			$vairableFee = self::VARIABLE_FEE_OTHER_COUNTRIES;
		}

		$paypalTotal = $total->divide( (100 - $vairableFee) / 100 )
		                     ->add( $this->getMoney( self::FIXED_FEE ) );

		return $paypalTotal->subtract( $total );
	}
}