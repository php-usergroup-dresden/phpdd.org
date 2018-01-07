<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Bridges;

use hollodotme\FluidValidator\Interfaces\ProvidesValuesToValidate;

/**
 * Class UserInput
 * @package PHPUGDD\PHPDD\Website\Application\Bridges
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
		return $this->input[ $var ] ?? null;
	}
}
