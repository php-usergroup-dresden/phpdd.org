<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits;

use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountPrice;

/**
 * Trait DiscountItemProviding
 * @package PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits
 */
trait DiscountItemProviding
{
	protected function getDiscountItem( string $name, string $code, string $description, Money $discountPrice ) : DiscountItem
	{
		return new DiscountItem(
			new DiscountName( $name ),
			new DiscountCode( $code ),
			new DiscountDescription( $description ),
			new DiscountPrice( $discountPrice )
		);
	}
}
