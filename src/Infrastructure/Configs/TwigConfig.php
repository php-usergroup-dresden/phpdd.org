<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Infrastructure\Configs;

/**
 * Class TwigConfig
 * @package PHPUGDD\PHPDD\Website\Infrastructure\Configs
 */
final class TwigConfig
{
	/** @var array */
	private $configData;

	public function __construct()
	{
		$this->configData = require __DIR__ . '/../../../config/Twig.php';
	}

	public function getSearchPaths() : array
	{
		return (array)$this->configData['searchPaths'];
	}

	public function getCacheDir() : string
	{
		return (string)$this->configData['cacheDir'];
	}

	public function isDebugEnabled() : bool
	{
		return (bool)$this->configData['debugEnabled'];
	}
}
