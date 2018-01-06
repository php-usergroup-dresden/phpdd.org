<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountPrice;

/**
 * Class DiscountItem
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class DiscountItem
{
	/** @var DiscountName */
	private $name;

	/** @var DiscountCode */
	private $code;

	/** @var DiscountDescription */
	private $description;

	/** @var DiscountPrice */
	private $discountPrice;

	public function __construct( DiscountName $name, DiscountCode $code, DiscountDescription $description, DiscountPrice $discountPrice )
	{
		$this->name          = $name;
		$this->code          = $code;
		$this->description   = $description;
		$this->discountPrice = $discountPrice;
	}

	public function getName() : DiscountName
	{
		return $this->name;
	}

	public function getCode() : DiscountCode
	{
		return $this->code;
	}

	public function getDescription() : DiscountDescription
	{
		return $this->description;
	}

	public function getDiscountPrice() : DiscountPrice
	{
		return $this->discountPrice;
	}
}
