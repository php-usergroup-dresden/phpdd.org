<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

final class PaymentFee
{
	/** @var Money */
	private $money;

	/**
	 * @param Money $money
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( Money $money )
	{
		$this->guardMoneyIsValid( $money );
		$this->money = $money;
	}

	/**
	 * @param Money $money
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardMoneyIsValid( Money $money ) : void
	{
		if ( $money->isNegative() )
		{
			throw new InvalidArgumentException(
				'Invalid money amount for payment fee provided: ' . $money->getAmount()
			);
		}
	}

	public function getMoney() : Money
	{
		return $this->money;
	}
}