<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling;

/**
 * Class Severity
 * @package PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling
 */
abstract class Severity
{
	public const DEBUG   = 'debug';

	public const INFO    = 'info';

	public const WARNING = 'warning';

	public const ERROR   = 'error';

	public const FATAL   = 'fatal';
}
