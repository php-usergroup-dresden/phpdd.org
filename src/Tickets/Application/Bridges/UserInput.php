<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Bridges;

use hollodotme\FluidValidator\Interfaces\ProvidesValuesToValidate;
use function is_iterable;
use function is_string;

/**
 * Class UserInput
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Bridges
 */
final class UserInput implements ProvidesValuesToValidate
{
	/** @var array */
	private $input;

	public function __construct( array $input )
	{
		$this->input = $input;
	}

	public function getValueToValidate( $var )
	{
		if ( isset( $this->input[ $var ] ) )
		{
			return $this->trimValue( $this->input[ $var ] );
		}

		return null;
	}

	private function trimValue( $value )
	{
		if ( is_string( $value ) )
		{
			return trim( $value );
		}

		if ( is_iterable( $value ) )
		{
			foreach ( $value as &$val )
			{
				$val = $this->trimValue( $val );
			}
			unset( $val );
		}

		return $value;
	}
}
