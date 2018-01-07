<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Infrastructure\Rendering;

use PHPUGDD\PHPDD\Website\Infrastructure\Configs\TwigConfig;

/**
 * Class Twig
 * @package PHPUGDD\PHPDD\Website\Infrastructure\Rendering
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

	public function renderWithData( string $template, array $data ) : string
	{
		return $this->environment->render( $template, $data );
	}
}
