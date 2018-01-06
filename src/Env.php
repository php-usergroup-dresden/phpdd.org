<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website;

use PHPUGDD\PHPDD\Website\Infrastructure\Configs\SentryConfig;
use PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Interfaces\ProvidesInfrastructure;

/**
 * Class Env
 * @package PHPUGDD\PHPDD\Website
 */
final class Env extends AbstractObjectPool implements ProvidesInfrastructure
{
	public function getErrorHandler() : SentryClient
	{
		return $this->getSharedInstance(
			'sentryClient',
			function ()
			{
				$config = new SentryConfig();

				return new SentryClient( $config );
			}
		);
	}
}