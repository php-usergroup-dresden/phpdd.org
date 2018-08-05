<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\TwigConfig;

/**
 * Class Twig
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering
 */
final class Twig
{
	/** @var \Twig_Environment */
	private $environment;

	public function __construct( TwigConfig $config )
	{
		$this->initRenderer( $config );
	}

	private function initRenderer( TwigConfig $config ) : void
	{
		$loader            = new \Twig_Loader_Filesystem( $config->getSearchPaths() );
		$this->environment = new \Twig_Environment(
			$loader,
			[
				'debug'      => $config->isDebugEnabled(),
				'cache'      => $config->getCacheDir(),
				'autoescape' => 'html',
			]
		);

		if ( $config->isDebugEnabled() )
		{
			$this->environment->addExtension( new \Twig_Extension_Debug() );
		}
	}

	public function addFilter( \Twig_Filter $filter ) : void
	{
		$this->environment->addFilter( $filter );
	}

	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 * @return string
	 */
	public function renderWithData( string $template, array $data ) : string
	{
		return $this->environment->render( $template, $data );
	}
}
