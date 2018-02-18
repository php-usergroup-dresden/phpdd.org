<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

/**
 * Class SessionHandlerConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs
 */
final class SessionHandlerConfig
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public function getName() : string
	{
		return (string)$this->configData['name'];
	}

	public function getHandler() : string
	{
		return (string)$this->configData['handler'];
	}

	public function getSavePath() : string
	{
		return (string)$this->configData['savePath'];
	}

	public function getMaxLifetime() : float
	{
		return (float)$this->configData['maxLifetime'];
	}
}
