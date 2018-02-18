<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

/**
 * Class SessionConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs
 */
final class SessionConfig
{
	/** @var array */
	private $configData;

	public function __construct()
	{
		$this->configData = require __DIR__ . '/../../../../config/Session.php';
	}

	public function getHandlerConfig() : SessionHandlerConfig
	{
		return new SessionHandlerConfig( $this->configData['sessionHandler'] );
	}

	public function getCookieConfig() : SessionCookieConfig
	{
		return new SessionCookieConfig( $this->configData['cookieSettings'] );
	}
}
