<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use PHPUGDD\PHPDD\Website\Infrastructure\Configs\SentryConfig;
use PHPUGDD\PHPDD\Website\Infrastructure\Configs\TwigConfig;
use PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Infrastructure\Rendering\Twig;
use PHPUGDD\PHPDD\Website\Infrastructure\Session;
use PHPUGDD\PHPDD\Website\Interfaces\ProvidesInfrastructure;

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

				$twig->addFilter( new \Twig_Filter( 'formatDate', [$this->getDateFormatter(), 'formatDateValue'] ) );
				$twig->addFilter( new \Twig_Filter( 'formatDateTime', [$this->getDateTimeFormatter(), 'formatDateValue'] ) );
				$twig->addFilter( new \Twig_Filter( 'formatMoney', [$this->getMoneyFormatter(), 'formatMoneyValue'] ) );

				return $twig;
			}
		);
	}
}
