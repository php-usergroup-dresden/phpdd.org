<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe\Interfaces\ConfiguresStripeClient;

final class StripeConfig implements ConfiguresStripeClient
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/Stripe.php';

		return new self( $configData );
	}

	public function getApiSecretKey() : string
	{
		return (string)$this->configData['apiSecretKey'];
	}

	public function getApiPublicKey() : string
	{
		return (string)$this->configData['apiPublicKey'];
	}

	public function getStatementDescriptor() : string
	{
		return (string)$this->configData['statementDescriptor'];
	}
}