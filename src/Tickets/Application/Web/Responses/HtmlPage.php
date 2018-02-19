<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses;

use IceHawk\IceHawk\Constants\HttpCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\ProjectConfig;
use PHPUGDD\PHPDD\Website\Tickets\Traits\InfrastructureInjecting;

/**
 * Class Page
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses
 */
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
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 */
	public function respond( string $template, array $data, int $httpCode = HttpCode::OK ) : void
	{
		$templateRenderer = $this->getEnv()->getTemplateRenderer();

		header( 'Content-Type: text/html; charset=utf-8', true, $httpCode );

		$content       = $templateRenderer->renderWithData( $template, $this->getMergedData( $data ) );
		$projectConfig = $this->getProjectConfig();

		$search  = array_keys( $projectConfig->getReplacements() );
		$replace = array_values( $projectConfig->getReplacements() );
		$content = str_replace( $search, $replace, $content );

		echo $content;

		flush();
	}

	private function getProjectConfig() : ProjectConfig
	{
		if ( null === $this->projectConfig )
		{
			$projectConfigFile = __DIR__ . '/../../../../../Project2018.json';
			$projectConfigData = $this->getDataFromProjectConfig( $projectConfigFile );
			$projectConfigDir  = \dirname( $projectConfigFile );

			$this->projectConfig = new ProjectConfig( $projectConfigDir, $projectConfigData );
		}

		return $this->projectConfig;
	}

	private function getDataFromProjectConfig( string $projectConfigFile ) : array
	{
		$configData = file_get_contents( $projectConfigFile );

		return json_decode( $configData, true );
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
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
