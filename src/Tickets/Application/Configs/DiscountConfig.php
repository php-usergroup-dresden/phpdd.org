<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;

final class DiscountConfig
{
	/** @var DiscountName */
	private $name;

	/** @var DiscountDescription */
	private $description;

	/** @var DiscountPrice */
	private $discount;

	/** @var array|TicketId[] */
	private $allowedTickets;

	/** @var array|DiscountCode[] */
	private $codes;

	/**
	 * @param DiscountName         $name
	 * @param DiscountDescription  $description
	 * @param DiscountPrice        $discount
	 * @param array|TicketId[]     $allowedTickets
	 * @param array|DiscountCode[] $codes
	 */
	public function __construct(
		DiscountName $name,
		DiscountDescription $description,
		DiscountPrice $discount,
		$allowedTickets,
		$codes
	)
	{
		$this->name           = $name;
		$this->description    = $description;
		$this->discount       = $discount;
		$this->allowedTickets = $allowedTickets;
		$this->codes          = $codes;
	}

	public function getName() : DiscountName
	{
		return $this->name;
	}

	public function getDescription() : DiscountDescription
	{
		return $this->description;
	}

	public function getDiscount() : DiscountPrice
	{
		return $this->discount;
	}

	public function getAllowedTickets() : array
	{
		return $this->allowedTickets;
	}

	public function getCodes() : array
	{
		return $this->codes;
	}
}