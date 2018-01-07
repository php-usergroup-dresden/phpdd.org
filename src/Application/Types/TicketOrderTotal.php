<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;

/**
 * Class TicketOrderTotal
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class TicketOrderTotal
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
			throw new InvalidArgumentException( 'Invalid money amount for ticket order total provided: ' . $money->getAmount() );
		}
	}

	public function getMoney() : Money
	{
		return $this->money;
	}
}
