<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Interfaces\ConfiguresPaypalClient;

final class PaypalClientConfig implements ConfiguresPaypalClient
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/Paypal.php';

		return new self( $configData );
	}

	public function getAuthEndpoint() : string
	{
		return (string)$this->configData['authEndpoint'];
	}

	public function getClientId() : string
	{
		return (string)$this->configData['clientId'];
	}

	public function getClientSecret() : string
	{
		return (string)$this->configData['clientSecret'];
	}

	public function getRedirectUrl() : string
	{
		return (string)$this->configData['redirectUrl'];
	}

	public function getCancelUrl() : string
	{
		return (string)$this->configData['cancelUrl'];
	}

	public function getSuccessUrl() : string
	{
		return (string)$this->configData['successUrl'];
	}

	public function getScopes() : string
	{
		return (string)$this->configData['scopes'];
	}
}