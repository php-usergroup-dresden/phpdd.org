<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe\Interfaces;

interface ConfiguresStripeClient
{
	public function getApiSecretKey() : string;

	public function getApiPublicKey() : string;

	public function getStatementDescriptor() : string;
}