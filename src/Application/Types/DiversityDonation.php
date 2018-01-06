<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Money\Money;

/**
 * Class DiversityDonation
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class DiversityDonation
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
