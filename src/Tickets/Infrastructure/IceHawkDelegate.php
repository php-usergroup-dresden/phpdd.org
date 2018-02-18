<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\SetsUpEnvironment;
use PHPUGDD\PHPDD\Website\Tickets\Env;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\SessionConfig;
use PHPUGDD\PHPDD\Website\Tickets\Traits\InfrastructureInjecting;

/**
 * Class IceHawkDelegate
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure
 */
final class IceHawkDelegate implements SetsUpEnvironment
{
	use InfrastructureInjecting;

	public function setUpGlobalVars() : void
	{
		date_default_timezone_set( Env::TIMEZONE );
	}

	/**
	 * @param ProvidesRequestInfo $requestInfo
	 *
	 * @throws \Raven_Exception
	 */
	public function setUpErrorHandling( ProvidesRequestInfo $requestInfo ) : void
	{
		$this->getEnv()->getErrorHandler()->install();
	}

	public function setUpSessionHandling( ProvidesRequestInfo $requestInfo ) : void
	{
		$sessionConfig = new SessionConfig();
		$handlerConfig = $sessionConfig->getHandlerConfig();
		$cookieConfig  = $sessionConfig->getCookieConfig();

		ini_set( 'session.name', $handlerConfig->getName() );
		ini_set( 'session.save_handler', $handlerConfig->getHandler() );
		ini_set( 'session.save_path', $handlerConfig->getSavePath() );
		ini_set( 'session.gc_maxlifetime', (string)$handlerConfig->getMaxLifetime() );

		session_set_cookie_params(
			$cookieConfig->getLifetime(),
			$cookieConfig->getPath(),
			$cookieConfig->getDomain(),
			$cookieConfig->isSecure(),
			$cookieConfig->isHttpOnly()
		);
	}
}
