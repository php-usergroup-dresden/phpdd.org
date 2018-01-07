<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Configs;

use IceHawk\IceHawk\Defaults\Traits\DefaultCookieProviding;
use IceHawk\IceHawk\Defaults\Traits\DefaultEventSubscribing;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestBypassing;
use IceHawk\IceHawk\Defaults\Traits\DefaultRequestInfoProviding;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use IceHawk\IceHawk\Routing\Patterns\NamedRegExp;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\WriteRoute;
use PHPUGDD\PHPDD\Website\Application\FinalResponders\FinalReadResponder;
use PHPUGDD\PHPDD\Website\Application\FinalResponders\FinalWriteResponder;
use PHPUGDD\PHPDD\Website\Traits\InfrastructureInjecting;

/**
 * Class IceHawkConfig
 * @package PHPUGDD\PHPDD\Website\Application\Configs
 */
final class IceHawkConfig implements ConfiguresIceHawk
{
	use InfrastructureInjecting;

	use DefaultCookieProviding;
	use DefaultEventSubscribing;
	use DefaultRequestBypassing;
	use DefaultRequestInfoProviding;

	public function getReadRoutes() : \Generator
	{
		$routes = require __DIR__ . '/../../../config/ReadRoutes.php';

		foreach ( (array)$routes as $pattern => $handlerClass )
		{
			yield new ReadRoute(
				new NamedRegExp( $pattern, 'i' ),
				new $handlerClass( $this->getEnv() )
			);
		}
	}

	public function getWriteRoutes() : \Generator
	{
		$routes = require __DIR__ . '/../../../config/WriteRoutes.php';

		foreach ( (array)$routes as $pattern => $handlerClass )
		{
			yield new WriteRoute(
				new NamedRegExp( $pattern, 'i' ),
				new $handlerClass( $this->getEnv() )
			);
		}
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
