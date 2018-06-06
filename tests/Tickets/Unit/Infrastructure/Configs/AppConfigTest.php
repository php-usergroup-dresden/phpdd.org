<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\AppConfig;
use PHPUnit\Framework\TestCase;

final class AppConfigTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetBaseUrl() : void
	{
		$appConfig = new AppConfig( ['baseUrl' => 'https://2018.phpdd.dev'] );

		$this->assertSame( 'https://2018.phpdd.dev', $appConfig->getBaseUrl() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInstanceFromConfigFile() : void
	{
		/** @noinspection UnnecessaryAssertionInspection */
		$this->assertInstanceOf( AppConfig::class, AppConfig::fromConfigFile() );
	}
}
