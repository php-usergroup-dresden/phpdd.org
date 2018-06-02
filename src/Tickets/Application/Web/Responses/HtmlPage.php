<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses;

use IceHawk\IceHawk\Constants\HttpCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\ProjectConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Traits\InfrastructureInjecting;
use function dirname;
use function file_get_contents;
use function file_put_contents;

final class HtmlPage
{
	use InfrastructureInjecting;

	/** @var ProjectConfig */
	private $projectConfig;

	/**
	 * @param string $template
	 * @param array  $data
	 * @param int    $httpCode
	 *
	 * @throws RuntimeException
	 */
	public function respond( string $template, array $data, int $httpCode = HttpCode::OK ) : void
	{
		header( 'Content-Type: text/html; charset=utf-8', true, $httpCode );
		echo $this->getContent( $template, $data );

		flush();
	}

	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @throws RuntimeException
	 * @return string
	 */
	private function getContent( string $template, array $data ) : string
	{
		$templateRenderer = $this->getEnv()->getTemplateRenderer();

		$content       = $templateRenderer->renderWithData( $template, $this->getMergedData( $data ) );
		$projectConfig = $this->getProjectConfig();

		$search  = array_keys( $projectConfig->getReplacements() );
		$replace = array_values( $projectConfig->getReplacements() );
		$content = str_replace( $search, $replace, $content );

		return $content;
	}

	/**
	 * @param string $filePath
	 * @param string $template
	 * @param array  $data
	 *
	 * @throws RuntimeException
	 * @return bool
	 */
	public function saveToFile( string $filePath, string $template, array $data ) : bool
	{
		$content = $this->getContent( $template, $data );

		return (bool)file_put_contents( $filePath, $content );
	}

	public function respondWithFile( string $filePath, int $httpCode = HttpCode::OK ) : void
	{
		header( 'Content-Type: text/html; charset=utf-8', true, $httpCode );
		echo file_get_contents( $filePath );
		flush();
	}

	private function getProjectConfig() : ProjectConfig
	{
		if ( null === $this->projectConfig )
		{
			$projectConfigFile = dirname( __DIR__, 5 ) . '/Project2018.json';
			$projectConfigData = $this->getDataFromProjectConfig( $projectConfigFile );
			$projectConfigDir  = dirname( $projectConfigFile );

			$this->projectConfig = new ProjectConfig( $projectConfigDir, $projectConfigData );
		}

		return $this->projectConfig;
	}

	private function getDataFromProjectConfig( string $projectConfigFile ) : array
	{
		$appConfig = $this->getEnv()->getAppConfig();
		$jsonData  = file_get_contents( $projectConfigFile );

		$configData            = json_decode( $jsonData, true );
		$configData['baseUrl'] = $appConfig->getBaseUrl();

		return $configData;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws RuntimeException
	 */
	private function getMergedData( array $data ) : array
	{
		$projectConfig = $this->getProjectConfig();
		$page          = $projectConfig->getPageConfigForUri( '/tickets.html' );

		return array_merge(
			[
				'project' => $projectConfig,
				'page'    => $page,
			],
			$data
		);
	}
}
