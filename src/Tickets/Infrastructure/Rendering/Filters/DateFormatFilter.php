<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters;

/**
 * Class DateFormatFilter
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Rendering\Filters
 */
final class DateFormatFilter
{
	/** @var \IntlDateFormatter */
	private $formatter;

	public function __construct( \IntlDateFormatter $formatter )
	{
		$this->formatter = $formatter;
	}

	public function formatDateValue( $dateValue ) : string
	{
		if ( $dateValue instanceof \DateTimeInterface )
		{
			return $this->formatter->format( $dateValue->getTimestamp() );
		}

		if ( \is_string( $dateValue ) )
		{
			return $this->formatter->format( $dateValue );
		}

		return '';
	}
}
