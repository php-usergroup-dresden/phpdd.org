<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

final class AppConfig
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/App.php';

		return new self( $configData );
	}

	public function getBaseUrl() : string
	{
		return (string)$this->configData['baseUrl'];
	}
}