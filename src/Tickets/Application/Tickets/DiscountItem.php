<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use function in_array;

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

	/** @var array|TicketId[] */
	private $allowedTickets;

	public function __construct(
		DiscountName $name,
		DiscountCode $code,
		DiscountDescription $description,
		DiscountPrice $discountPrice,
		array $allowedTickets
	)
	{
		$this->name           = $name;
		$this->code           = $code;
		$this->description    = $description;
		$this->discountPrice  = $discountPrice;
		$this->allowedTickets = $allowedTickets;
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

	public function isAllowedForTicket( TicketId $ticketId ) : bool
	{
		return in_array( $ticketId, $this->allowedTickets, false );
	}
}
