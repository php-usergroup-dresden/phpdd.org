<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\PaypalClientConfig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Interfaces\ConfiguresPaypalClient;
use PHPUnit\Framework\TestCase;

final class PaypalClientConfigTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetValuesFromConfigData() : void
	{
		$configData = [
			'authEndpoint' => 'sandbox',
			'clientId'     => 'Test-Client-ID',
			'clientSecret' => 'Test-Client-Secret',
			'redirectUrl'  => 'https://2018.phpdd.dev/tickets/',
			'scopes'       => 'profile email address phone https://uri.paypal.com/services/paypalattributes',
			'cancelUrl'    => 'https://2018.phpdd.dev/tickets/paypal-canceled',
			'successUrl'   => 'https://2018.phpdd.dev/tickets/paypal-success',
		];

		$paypalClientConfig = new PaypalClientConfig( $configData );

		$this->assertSame( 'sandbox', $paypalClientConfig->getAuthEndpoint() );
		$this->assertSame( 'Test-Client-ID', $paypalClientConfig->getClientId() );
		$this->assertSame( 'Test-Client-Secret', $paypalClientConfig->getClientSecret() );
		$this->assertSame( 'https://2018.phpdd.dev/tickets/', $paypalClientConfig->getRedirectUrl() );
		$this->assertSame(
			'profile email address phone https://uri.paypal.com/services/paypalattributes',
			$paypalClientConfig->getScopes()
		);
		$this->assertSame( 'https://2018.phpdd.dev/tickets/paypal-canceled', $paypalClientConfig->getCancelUrl() );
		$this->assertSame( 'https://2018.phpdd.dev/tickets/paypal-success', $paypalClientConfig->getSuccessUrl() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInstanceFromConfigFile() : void
	{
		/** @noinspection UnnecessaryAssertionInspection */
		$this->assertInstanceOf( PaypalClientConfig::class, PaypalClientConfig::fromConfigFile() );
		/** @noinspection UnnecessaryAssertionInspection */
		$this->assertInstanceOf( ConfiguresPaypalClient::class, PaypalClientConfig::fromConfigFile() );
	}
}
