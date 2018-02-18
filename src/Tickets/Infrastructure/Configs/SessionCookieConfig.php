<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

/**
 * Class SessionCookieConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs
 */
final class SessionCookieConfig
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public function getLifetime() : float
	{
		return (float)$this->configData['lifetime'];
	}

	public function getPath() : string
	{
		return (string)$this->configData['path'];
	}

	public function getDomain() : string
	{
		return (string)$this->configData['domain'];
	}

	public function isSecure() : bool
	{
		return (bool)$this->configData['secure'];
	}

	public function isHttpOnly() : bool
	{
		return (bool)$this->configData['httpOnly'];
	}
}
