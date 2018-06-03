<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces\ConfiguresSlackClient;

final class SlackConfig implements ConfiguresSlackClient
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/Slack.php';

		return new self( $configData );
	}

	public function getWebHookUrl() : string
	{
		return (string)$this->configData['webHookUrl'];
	}
}