<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;

/**
 * Class TicketPrice
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class TicketPrice
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
		if ( $money->isNegative() || $money->isZero() )
		{
			throw new InvalidArgumentException( 'Invalid money amount for ticket price provided: ' . $money->getAmount() );
		}
	}

	public function getMoney() : Money
	{
		return $this->money;
	}
}
