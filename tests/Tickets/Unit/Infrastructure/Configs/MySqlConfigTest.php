<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\MySqlConfig;
use PHPUnit\Framework\TestCase;

final class MySqlConfigTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetValuesFromConfigData() : void
	{
		$configData = [
			'host'     => 'phpdd18-mysql',
			'port'     => 3306,
			'database' => 'phpdd18',
			'user'     => 'user',
			'password' => 'password',
		];

		$mySqlConfig = new MySqlConfig( $configData );

		$this->assertSame( 'mysql:host=phpdd18-mysql;port=3306;dbname=phpdd18', $mySqlConfig->getDsn() );
		$this->assertSame( 'user', $mySqlConfig->getUser() );
		$this->assertSame( 'password', $mySqlConfig->getPassword() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInstanceFromConfigFile() : void
	{
		/** @noinspection UnnecessaryAssertionInspection */
		$this->assertInstanceOf( MySqlConfig::class, MySqlConfig::fromConfigFile() );
	}
}
