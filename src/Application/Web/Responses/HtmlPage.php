<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Web\Responses;

use PHPUGDD\PHPDD\Website\Traits\InfrastructureInjecting;

/**
 * Class Page
 * @package PHPUGDD\PHPDD\Website\Application\Web\Responses
 */
final class HtmlPage
{
	use InfrastructureInjecting;

	public function respond( string $template, array $data, int $httpCode ) : void
	{
		$templateRenderer = $this->getEnv()->getTemplateRenderer();

		header( 'Content-Type: text/html; charset=utf-8', true, $httpCode );
		echo $templateRenderer->renderWithData( $template, $data );
		flush();
	}
}
