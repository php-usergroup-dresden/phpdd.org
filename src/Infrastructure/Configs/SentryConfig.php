<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Infrastructure\Configs;

/**
 * Class SentryConfig
 * @package PHPUGDD\PHPDD\Website\Infrastructure\Configs
 */
final class SentryConfig
{
	/** @var array */
	private $configData;

	public function __construct()
	{
		$this->configData = require __DIR__ . '/../../../config/Sentry.php';
	}

	public function getDsn() : string
	{
		return (string)$this->configData['dsn'];
	}

	public function getErrorReporting() : int
	{
		return (int)$this->configData['errorReporting'];
	}

	public function displayErrors() : bool
	{
		return (bool)$this->configData['displayErrors'];
	}

	public function getEnvironment() : string
	{
		return (string)$this->configData['environment'];
	}

	public function getRelease() : string
	{
		return (string)$this->configData['release'];
	}
}
