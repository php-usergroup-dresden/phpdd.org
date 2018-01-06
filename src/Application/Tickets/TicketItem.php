<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use Money\Currency;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountPrice;

/**
 * Class TicketItem
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketItem
{
	/** @var Ticket */
	private $ticket;

	/** @var AttendeeName */
	private $attendeeName;

	/** @var DiscountItem */
	private $discountItem;

	public function __construct( Ticket $ticket, AttendeeName $attendeeName )
	{
		$this->ticket       = $ticket;
		$this->attendeeName = $attendeeName;
		$this->discountItem = new DiscountItem(
			new DiscountName( '' ),
			new DiscountCode( '' ),
			new DiscountDescription( '' ),
			new DiscountPrice( new Money( 0, new Currency( 'EUR' ) ) )
		);
	}

	public function getTicket() : Ticket
	{
		return $this->ticket;
	}

	public function getAttendeeName() : AttendeeName
	{
		return $this->attendeeName;
	}

	public function getDiscountItem() : DiscountItem
	{
		return $this->discountItem;
	}
}
