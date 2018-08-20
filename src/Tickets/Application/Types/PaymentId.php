<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use function bin2hex;
use function random_bytes;

final class PaymentId extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( '' === trim( $value ) )
		{
			throw new InvalidArgumentException( 'Payment ID cannot be empty.' );
		}
	}

	/**
	 * @throws \Exception
	 * @return PaymentId
	 */
	public static function newRefundId() : self
	{
		$uuid = bin2hex( random_bytes( 16 ) );

		return new self( 'REFUND-' . $uuid );
	}
}