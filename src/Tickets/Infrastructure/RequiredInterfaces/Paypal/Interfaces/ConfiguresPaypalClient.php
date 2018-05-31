<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Interfaces;

interface ConfiguresPaypalClient
{
	public function getAuthEndpoint() : string;

	public function getClientId() : string;

	public function getClientSecret() : string;

	public function getRedirectUrl() : string;

	public function getCancelUrl() : string;

	public function getSuccessUrl() : string;

	public function getScopes() : string;
}