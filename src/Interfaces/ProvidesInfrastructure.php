<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Interfaces;

use PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Infrastructure\Session;

/**
 * Interface ProvidesInfrastructure
 * @package PHPUGDD\PHPDD\Website\Application\Interfaces
 */
interface ProvidesInfrastructure
{
	public function getErrorHandler() : SentryClient;

	public function getSession() : Session;
}
