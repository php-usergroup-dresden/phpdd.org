<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses;

use IceHawk\IceHawk\Constants\HttpCode;
use PHPUGDD\PHPDD\Website\Tickets\Traits\InfrastructureInjecting;

/**
 * Class Page
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses
 */
final class HtmlPage
{
	use InfrastructureInjecting;

	public function respond( string $template, array $data, int $httpCode = HttpCode::OK ) : void
	{
		$templateRenderer = $this->getEnv()->getTemplateRenderer();

		header( 'Content-Type: text/html; charset=utf-8', true, $httpCode );
		echo $templateRenderer->renderWithData( $template, $data );
		flush();
	}
}
