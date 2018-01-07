<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Interfaces;

use Money\MoneyFormatter;
use PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Infrastructure\Rendering\Twig;
use PHPUGDD\PHPDD\Website\Infrastructure\Session;

/**
 * Interface ProvidesInfrastructure
 * @package PHPUGDD\PHPDD\Website\Application\Interfaces
 */
interface ProvidesInfrastructure
{
	public function getErrorHandler() : SentryClient;

	public function getSession() : Session;

	public function getDateFormatter() : \IntlDateFormatter;

	public function getDateTimeFormatter() : \IntlDateFormatter;

	public function getMoneyFormatter() : MoneyFormatter;

	public function getTemplateRenderer() : Twig;
}
