<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

final class MySqlConfig
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		return new self( (array)require __DIR__ . '/../../../../config/MySql.php' );
	}

	public function getDsn() : string
	{
		return sprintf(
			'mysql:host=%s;port=%d;dbname=%s',
			(string)$this->configData['host'],
			(int)$this->configData['port'],
			(string)$this->configData['database']
		);
	}

	public function getUser() : string
	{
		return (string)$this->configData['user'];
	}

	public function getPassword() : string
	{
		return (string)$this->configData['password'];
	}
}