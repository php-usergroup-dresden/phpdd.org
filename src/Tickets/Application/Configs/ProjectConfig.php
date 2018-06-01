<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;

/**
 * Class ProjectConfig
 *
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Configs
 */
final class ProjectConfig
{
	/** @var string */
	private $configFileDir;

	/** @var array */
	private $configData;

	public function __construct( string $configFileDir, array $configData )
	{
		$this->configFileDir = $configFileDir;
		$this->configData    = $configData;
	}

	public function getName() : string
	{
		return $this->getValue( 'name' );
	}

	public function getBaseUrl() : string
	{
		return $this->getValue( 'baseUrl' );
	}

	/**
	 * @return string
	 * @throws RuntimeException
	 */
	public function getOutputDir() : string
	{
		$outputDir     = $this->getValue( 'outputDir' );
		$outputDirReal = realpath( $this->configFileDir . DIRECTORY_SEPARATOR . $outputDir );
		if ( !$outputDirReal || !is_dir( $outputDirReal ) )
		{
			throw new RuntimeException( 'Output directory not found: ' . $outputDir );
		}

		return $outputDirReal;
	}

	/**
	 * @return string
	 * @throws RuntimeException
	 */
	public function getContentsDir() : string
	{
		$contentsDir     = $this->getValue( 'contentsDir' );
		$contentsDirReal = realpath( $this->configFileDir . DIRECTORY_SEPARATOR . $contentsDir );
		if ( !$contentsDirReal || !is_dir( $contentsDirReal ) )
		{
			throw new RuntimeException( 'Content directory not found: ' . $contentsDir );
		}

		return $contentsDirReal;
	}

	public function getReplacements() : array
	{
		$replacements = $this->configData['replacements'] ?? [];

		$replacements['@baseUrl@'] = $this->getBaseUrl();
		$replacements['@name@']    = $this->getName();

		return $replacements;
	}

	private function getValue( $key ) : string
	{
		return $this->configData[ $key ] ?? '';
	}

	public function getPageConfigsAtLevel( int $pageLevel ) : \Generator
	{
		yield from $this->getPageConfigsByFilter(
			function ( array $pageConfig ) use ( $pageLevel )
			{
				return (($pageConfig['pageLevel'] ?? -1) === $pageLevel);
			}
		);
	}

	/**
	 * @return \Generator|PageConfig[]
	 */
	public function getAllPages() : \Generator
	{
		yield from $this->getPageConfigsByFilter();
	}

	/**
	 * @param callable $filter
	 *
	 * @return \Generator|PageConfig[]
	 */
	public function getPageConfigsByFilter( callable $filter = null ) : \Generator
	{
		$pagesConfig = $this->configData['pages'] ?? [];

		if ( null !== $filter )
		{
			$pagesConfig = array_filter( $pagesConfig, $filter, ARRAY_FILTER_USE_BOTH );
		}

		foreach ( $pagesConfig as $uri => $configData )
		{
			yield new PageConfig( $uri, $configData );
		}
	}

	/**
	 * @return array|PageConfig[][]
	 */
	public function getPageConfigsGroupedByTag() : array
	{
		$tagReferences = [];

		foreach ( $this->getPageConfigsByFilter() as $pageConfig )
		{
			foreach ( $pageConfig->getTags() as $tag )
			{
				if ( isset( $tagReferences[ $tag ] ) )
				{
					$tagReferences[ $tag ][] = $pageConfig;
				}
				else
				{
					$tagReferences[ $tag ] = [$pageConfig];
				}
			}
		}

		return $tagReferences;
	}

	public function getChildrenOf( PageConfig $pageConfig ) : \Generator
	{
		yield from $this->getPageConfigsByFilter(
			function ( /** @noinspection PhpUnusedParameterInspection */
				array $configData, string $uri ) use ( $pageConfig )
			{
				return \in_array( $uri, $pageConfig->getChildren(), true );
			}
		);
	}

	/**
	 * @param string $uri
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 * @return PageConfig
	 */
	public function getPageConfigForUri( string $uri ) : PageConfig
	{
		$pageConfigs = iterator_to_array(
			$this->getPageConfigsByFilter(
				function (
					/** @noinspection PhpUnusedParameterInspection */
					array $pageConfig, string $configUri
				) use ( $uri )
				{
					return ($configUri === $uri);
				}
			)
		);

		if ( \count( $pageConfigs ) === 1 )
		{
			return $pageConfigs[0];
		}

		throw new RuntimeException( 'Page config not found for URI: ' . $uri );
	}

	/**
	 * @param PageConfig $pageConfig
	 *
	 * @return PageConfig
	 * @throws RuntimeException
	 */
	private function getParentOf( PageConfig $pageConfig ) : PageConfig
	{
		$pageUri = $pageConfig->getUri();

		if ( $pageConfig->getPageLevel() === 1 )
		{
			throw new RuntimeException( 'Parent page config not found for URI: ' . $pageUri );
		}

		$parentLevel = $pageConfig->getPageLevel() - 1;

		$pageConfigs = iterator_to_array(
			$this->getPageConfigsByFilter(
				function ( array $configData ) use ( $parentLevel, $pageUri )
				{
					return (($configData['pageLevel'] ?? -1) === $parentLevel)
						   && \in_array( $pageUri, $configData['children'] ?? [], true );
				}
			)
		);

		if ( \count( $pageConfigs ) === 1 )
		{
			return $pageConfigs[0];
		}

		throw new RuntimeException( 'Parent page config not found for URI: ' . $pageUri );
	}

	public function getBreadCrumbFor( PageConfig $pageConfig ) : array
	{
		$breadCrumb = [$pageConfig->getUri() => $pageConfig->getNavName()];

		try
		{
			$parentPageConfig = $this->getParentOf( $pageConfig );
		}
		catch ( RuntimeException $e )
		{
			return $breadCrumb;
		}

		$breadCrumb = array_merge( $this->getBreadCrumbFor( $parentPageConfig ), $breadCrumb );

		return $breadCrumb;
	}

	public function getUrl( string $path ) : string
	{
		return $this->getBaseUrl() . $path;
	}
}
