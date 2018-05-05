<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;

/**
 * Trait DiscountItemProviding
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits
 */
trait DiscountItemProviding
{
	/**
	 * @param string $name
	 * @param string $code
	 * @param string $description
	 * @param \Money\Money $discountPrice
	 *
	 * @param array $allowedTickets
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @return \PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem
	 */
	protected function getDiscountItem(
		string $name,
		string $code,
		string $description,
		Money $discountPrice,
		array $allowedTickets
	) : DiscountItem
	{
		return new DiscountItem(
			new DiscountName( $name ),
			new DiscountCode( $code ),
			new DiscountDescription( $description ),
			new DiscountPrice( $discountPrice ),
			array_map(
				function ( $ticketName )
				{
					return new TicketName( $ticketName );
				},
				$allowedTickets
			)
		);
	}
}
