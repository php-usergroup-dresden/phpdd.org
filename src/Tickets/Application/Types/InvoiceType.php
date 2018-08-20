<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Invoices\InvoiceTypes;
use function in_array;

final class InvoiceType extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !in_array( $value, InvoiceTypes::ALL, true ) )
		{
			throw new InvalidArgumentException( 'Invalid invoice type: ' . $value );
		}
	}
}