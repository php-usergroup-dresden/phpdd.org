<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\DiscountConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesDiscountCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use function array_reduce;
use function in_array;

final class DiscountsConfig implements ProvidesDiscountCodes
{
	use MoneyProviding;

	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/Discounts.php';

		return new self( $configData );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @return Generator|DiscountConfig[]
	 */
	public function getDiscountConfigs() : Generator
	{
		foreach ( $this->configData as $name => $discountData )
		{
			$discountName        = new DiscountName( (string)$name );
			$discountDescription = new DiscountDescription( (string)$discountData['description'] );
			$discountPrice       = new DiscountPrice( $this->getMoney( (int)$discountData['discount'] ) );

			$allowedTickets = array_map(
				function ( $ticketId )
				{
					return new TicketId( (string)$ticketId );
				},
				(array)$discountData['allowedTickets']
			);

			$codes = array_map(
				function ( $code )
				{
					return new DiscountCode( (string)$code );
				},
				(array)$discountData['codes']
			);

			yield new DiscountConfig(
				$discountName,
				$discountDescription,
				$discountPrice,
				$allowedTickets,
				$codes
			);
		}
	}

	public function getDiscountCodesForTicketId( string $ticketId ) : array
	{
		$merger = function ( array $allCodes, array $discountConfig ) use ( $ticketId )
		{
			$codes = [];
			if ( in_array( $ticketId, (array)$discountConfig['allowedTickets'], true ) )
			{
				$codes = (array)$discountConfig['codes'];
			}

			return array_merge( $allCodes, $codes );
		};

		return (array)array_reduce( $this->configData, $merger, [] );
	}

	/**
	 * @param string $ticketId
	 * @param string $code
	 *
	 * @throws InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws DiscountConfigNotFoundException
	 * @return DiscountConfig
	 */
	public function getDiscountConfigByTicketIdAndCode( string $ticketId, string $code ) : DiscountConfig
	{
		foreach ( $this->configData as $name => $discountConfig )
		{
			if ( !in_array( $ticketId, (array)$discountConfig['allowedTickets'], true ) )
			{
				continue;
			}

			if ( !in_array( $code, (array)$discountConfig['codes'], true ) )
			{
				continue;
			}

			$discountName        = new DiscountName( (string)$name );
			$discountDescription = new DiscountDescription( (string)$discountConfig['description'] );
			$discountPrice       = new DiscountPrice( $this->getMoney( (int)$discountConfig['discount'] ) );

			$allowedTickets = array_map(
				function ( $ticketId )
				{
					return new TicketId( (string)$ticketId );
				},
				(array)$discountConfig['allowedTickets']
			);

			$codes = array_map(
				function ( $code )
				{
					return new DiscountCode( (string)$code );
				},
				(array)$discountConfig['codes']
			);

			return new DiscountConfig(
				$discountName,
				$discountDescription,
				$discountPrice,
				$allowedTickets,
				$codes
			);
		}

		throw new DiscountConfigNotFoundException( 'Could not find discount config.' );
	}
}