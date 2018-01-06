<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Infrastructure;

use IceHawk\IceHawk\Defaults\Traits\DefaultCookieProviding;
use IceHawk\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestBypassing;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestInfoProviding;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use PHPUGDD\PHPDD\Website\Application\FinalResponders\FinalReadResponder;
use PHPUGDD\PHPDD\Website\Application\FinalResponders\FinalWriteResponder;
use PHPUGDD\PHPDD\Website\Traits\InfrastructureInjecting;

/**
 * Class IceHawkConfig
 * @package PHPUGDD\PHPDD\Website\Infrastructure
 */
final class IceHawkConfig implements ConfiguresIceHawk
{
	use InfrastructureInjecting;

	use DefaultCookieProviding;
	use DefaultEventSubscribing;
	use DefaultRequestBypassing;
	use DefaultRequestInfoProviding;

	public function getReadRoutes()
	{
		return [];
	}

	public function getWriteRoutes()
	{
		return [];
	}

	public function getFinalReadResponder() : RespondsFinallyToReadRequest
	{
		return new FinalReadResponder( $this->getEnv() );
	}

	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest
	{
		return new FinalWriteResponder( $this->getEnv() );
	}
}
