<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\SentryConfig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\TwigConfig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters\DateFormatFilter;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters\MoneyFormatFilter;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Twig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Session;
use PHPUGDD\PHPDD\Website\Tickets\Interfaces\ProvidesInfrastructure;

/**
 * Class Env
 * @package PHPUGDD\PHPDD\Website
 */
final class Env extends AbstractObjectPool implements ProvidesInfrastructure
{
	public const LOCALE   = 'en_GB';

	public const TIMEZONE = 'Europe/Berlin';

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

	public function getSession() : Session
	{
		return $this->getSharedInstance(
			'session',
			function ()
			{
				if ( session_status() !== PHP_SESSION_ACTIVE )
				{
					session_start();
				}

				return new Session( $_SESSION );
			}
		);
	}

	public function getDateFormatter() : \IntlDateFormatter
	{
		return $this->getSharedInstance(
			'dateFormatter',
			function ()
			{
				return new \IntlDateFormatter( self::LOCALE, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE );
			}
		);
	}

	public function getDateTimeFormatter() : \IntlDateFormatter
	{
		return $this->getSharedInstance(
			'dateTimeFormatter',
			function ()
			{
				return new \IntlDateFormatter( self::LOCALE, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT );
			}
		);
	}

	public function getMoneyFormatter() : MoneyFormatter
	{
		return $this->getSharedInstance(
			'moneyFormatter',
			function ()
			{
				$currencies = new ISOCurrencies();

				$numberFormatter = new \NumberFormatter( self::LOCALE, \NumberFormatter::CURRENCY );

				return new IntlMoneyFormatter( $numberFormatter, $currencies );
			}
		);
	}

	public function getTemplateRenderer() : Twig
	{
		return $this->getSharedInstance(
			'templateRenderer',
			function ()
			{
				$twigConfig = new TwigConfig();
				$twig       = new Twig( $twigConfig );

				$dateFormatter     = new DateFormatFilter( $this->getDateFormatter() );
				$dateTimeFormatter = new DateFormatFilter( $this->getDateTimeFormatter() );
				$moneyFormatter    = new MoneyFormatFilter( $this->getMoneyFormatter() );

				$twig->addFilter( new \Twig_Filter( 'formatDate', [$dateFormatter, 'formatDateValue'] ) );
				$twig->addFilter( new \Twig_Filter( 'formatDateTime', [$dateTimeFormatter, 'formatDateValue'] ) );
				$twig->addFilter( new \Twig_Filter( 'formatMoney', [$moneyFormatter, 'formatMoneyValue'] ) );

				return $twig;
			}
		);
	}
}
