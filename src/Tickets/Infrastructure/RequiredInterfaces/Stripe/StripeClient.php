<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe\Interfaces\ConfiguresStripeClient;
use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Stripe;
use Throwable;

final class StripeClient
{
	/** @var ConfiguresStripeClient */
	private $config;

	public function __construct( ConfiguresStripeClient $config )
	{
		$this->config = $config;
	}

	/**
	 * @noinspection PhpDocRedundantThrowsInspection
	 *
	 * @param StripeExecuteRequest $request
	 *
	 * @throws \Stripe\Error\Card
	 * @throws \Stripe\Error\RateLimit
	 * @throws \Stripe\Error\InvalidRequest
	 * @throws \Stripe\Error\Authentication
	 * @throws \Stripe\Error\ApiConnection
	 * @throws \Stripe\Error\Base
	 * @throws Throwable
	 *
	 * @return ApiResource
	 */
	public function executePayment( StripeExecuteRequest $request ) : ApiResource
	{
		Stripe::setApiKey( $this->config->getApiSecretKey() );

		$charge = Charge::create(
			[
				'amount'               => $request->getAmount(),
				'currency'             => $request->getCurrencyCode(),
				'description'          => $request->getDescription(),
				'source'               => $request->getToken(),
				'statement_descriptor' => $this->config->getStatementDescriptor(),
			]
		);

		return $charge;
	}
}