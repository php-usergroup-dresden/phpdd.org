<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling;

use PHPUGDD\PHPDD\Website\Infrastructure\Configs\SentryConfig;

/**
 * Class SentryClient
 * @package PHPUGDD\PHPDD\Website\Infrastructure\ErrorHandling
 */
final class SentryClient
{
	/** @var SentryConfig */
	private $config;

	/** @var \Raven_Client */
	private $client;

	public function __construct( SentryConfig $config )
	{
		$this->config = $config;
		$this->client = new \Raven_Client( $config->getDsn() );
		$this->client->setEnvironment( $config->getEnvironment() );
		$this->client->setRelease( $config->getRelease() );
		$this->setUpRuntimeConfig();
	}

	/**
	 * @throws \Raven_Exception
	 */
	public function install() : void
	{
		$this->setUpOutputHandler();
		$this->client->install();
	}

	private function setUpRuntimeConfig() : void
	{
		error_reporting( $this->config->getErrorReporting() );
		ini_set( 'display_errors', $this->config->displayErrors() ? 'On' : 'Off' );
	}

	private function setUpOutputHandler() : void
	{
		if ( $this->config->displayErrors() )
		{
			set_exception_handler( $this->getExceptionOutputClosure() );
		}
	}

	private function getExceptionOutputClosure() : \Closure
	{
		return function ( \Throwable $throwable )
		{
			printf(
				"Uncaught exception '%s' with message '%s' in %s:%s\n\nStack trace:\n%s\nthrown in %s on line %s\n",
				get_class( $throwable ),
				$throwable->getMessage(),
				$throwable->getFile(),
				$throwable->getLine(),
				$throwable->getTraceAsString(),
				$throwable->getFile(),
				$throwable->getLine()
			);
		};
	}

	public function captureException(
		\Throwable $throwable,
		string $severity = Severity::ERROR,
		array $additionalKeyValueData = []
	) : void
	{
		if ( $this->config->displayErrors() )
		{
			$closure = $this->getExceptionOutputClosure();
			$closure->call( $this, $throwable );
			$this->printAdditionalData( $additionalKeyValueData );
		}
		$this->client->captureException(
			$throwable,
			[
				'level' => $severity,
				'extra' => $additionalKeyValueData,
			]
		);
	}

	private function printAdditionalData( array $additionalKeyValueData ) : void
	{
		if ( !empty( $additionalKeyValueData ) )
		{
			echo "\nAdditional data:\n", print_r( $additionalKeyValueData, true );
		}
	}

	public function captureMessage(
		string $message,
		array $params = [],
		string $severity = Severity::INFO,
		array $additionalKeyValueData = []
	) : void
	{
		if ( $this->config->displayErrors() )
		{
			$this->printMessageWithSeverity( $severity, sprintf( $message, ...$params ) );
			$this->printAdditionalData( $additionalKeyValueData );
		}
		$this->client->captureMessage(
			$message,
			$params,
			[
				'level' => $severity,
				'extra' => $additionalKeyValueData,
			]
		);
	}

	private function printMessageWithSeverity( string $severity, string $message ) : void
	{
		echo strtoupper( $severity ) . ': ' . $message . PHP_EOL;
	}

	public function captureLastError( string $severity = Severity::ERROR, array $additionalKeyValueData = [] ) : void
	{
		if ( $this->config->displayErrors() )
		{
			if ( null !== ($lastError = error_get_last()) )
			{
				$message = sprintf(
					'%s in %s on line %d',
					$lastError['message'] ?? '[no message given]',
					$lastError['file'] ?? '[no file given]',
					$lastError['line'] ?? '[no line given]'
				);
				$this->printMessageWithSeverity( $severity, $message );
				$this->printAdditionalData( $additionalKeyValueData );
			}
		}
		$this->client->captureLastError();
	}
}
