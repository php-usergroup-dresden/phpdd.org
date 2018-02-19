<?php declare(strict_types=1);
/**
 * @author  hollodotme
 * @license MIT (See LICENSE file)
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Configs;

/**
 * Class PageConfig
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Configs
 */
final class PageConfig
{
	/** @var string */
	private $uri;
	/** @var array */
	private $configData;

	public function __construct( string $uri, array $configData )
	{
		$this->uri        = $uri;
		$this->configData = $configData;
	}

	public function getUri() : string
	{
		return $this->uri;
	}

	public function getPageLevel() : int
	{
		return (int)$this->getValue( 'pageLevel' );
	}

	private function getValue( $key ) : string
	{
		return (string)($this->configData[ $key ] ?? '');
	}

	public function getPageTitle() : string
	{
		return $this->getValue( 'pageTitle' );
	}

	public function getDescription() : string
	{
		return $this->getValue( 'description' );
	}

	public function getNavName() : string
	{
		return $this->getValue( 'navName' );
	}

	public function getImageUrl() : string
	{
		return $this->getValue( 'imageUrl' );
	}

	public function getTags() : array
	{
		return $this->configData['tags'] ?? [];
	}

	public function getContentType() : string
	{
		return $this->getValue( 'contentType' );
	}

	public function getRenderer() : string
	{
		return $this->getValue( 'renderer' );
	}

	public function getTemplate() : string
	{
		return $this->getValue( 'template' );
	}

	public function getContentFile() : string
	{
		return $this->getValue( 'contentFile' );
	}

	public function getChildren() : array
	{
		return $this->configData['children'] ?? [];
	}

	public function hasChildren() : bool
	{
		return !empty( $this->getChildren() );
	}
}
