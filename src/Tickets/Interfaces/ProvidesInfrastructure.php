<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Interfaces;

use Money\MoneyFormatter;
use PDO;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Twig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Session;

/**
 * Interface ProvidesInfrastructure
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Interfaces
 */
interface ProvidesInfrastructure
{
	public function getErrorHandler() : SentryClient;

	public function getSession() : Session;

	public function getDateFormatter() : \IntlDateFormatter;

	public function getDateTimeFormatter() : \IntlDateFormatter;

	public function getMoneyFormatter() : MoneyFormatter;

	public function getTemplateRenderer() : Twig;

	public function getDatabase() : PDO;
}
