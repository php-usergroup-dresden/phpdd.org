<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Traits;

use PHPUGDD\PHPDD\Website\Interfaces\ProvidesInfrastructure;

/**
 * Trait InfrastructureInjection
 * @package PHPUGDD\PHPDD\Website\Traits
 */
trait InfrastructureInjecting
{
	/** @var ProvidesInfrastructure */
	private $env;

	public function __construct( ProvidesInfrastructure $env )
	{
		$this->env = $env;
	}

	final protected function getEnv() : ProvidesInfrastructure
	{
		return $this->env;
	}
}
