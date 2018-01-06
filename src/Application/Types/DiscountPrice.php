<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Money\Money;

/**
 * Class DiscountPrice
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class DiscountPrice
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
