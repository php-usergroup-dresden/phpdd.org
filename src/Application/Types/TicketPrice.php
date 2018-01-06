<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Money\Money;

/**
 * Class TicketPrice
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class TicketPrice
{
	/** @var Money */
	private $money;

	public function __construct( Money $money )
	{
		$this->money = $money;
	}

	public function getMoney() : Money
	{
		return $this->money;
	}
}
