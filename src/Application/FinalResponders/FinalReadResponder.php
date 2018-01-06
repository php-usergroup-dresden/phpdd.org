<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\FinalResponders;

use IceHawk\IceHawk\Constants\HttpCode;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use PHPUGDD\PHPDD\Website\Traits\InfrastructureInjecting;

/**
 * Class FinalReadResponder
 * @package PHPUGDD\PHPDD\Website\Application\FinalResponders
 */
final class FinalReadResponder implements RespondsFinallyToReadRequest
{
	use InfrastructureInjecting;

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request ) : void
	{
		$this->getEnv()->getErrorHandler()->captureException( $throwable );

		header( 'Content-Type: text/html; charset=utf-8', true, HttpCode::INTERNAL_SERVER_ERROR );
		readfile( __DIR__ . '/../../../public/2018/500.html' );
		flush();
	}
}
