<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;

/**
 * Class TicketOrderDiscountTotal
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class TicketOrderDiscountTotal
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
		if ( $money->isPositive() )
		{
			throw new InvalidArgumentException( 'Invalid money amount for ticket order discount total provided: ' . $money->getAmount() );
		}
	}

	public function getMoney() : Money
	{
		return $this->money;
	}
}
