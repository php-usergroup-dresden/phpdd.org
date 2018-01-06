<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\FinalResponders;

use IceHawk\IceHawk\Constants\HttpCode;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use PHPUGDD\PHPDD\Website\Traits\InfrastructureInjecting;

/**
 * Class FinalWriteResponder
 * @package PHPUGDD\PHPDD\Website\Application\FinalResponders
 */
final class FinalWriteResponder implements RespondsFinallyToWriteRequest
{
	use InfrastructureInjecting;

	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request ) : void
	{
		$this->getEnv()->getErrorHandler()->captureException( $throwable );

		header( 'Content-Type: text/html; charset=utf-8', true, HttpCode::INTERNAL_SERVER_ERROR );
		readfile( __DIR__ . '/../../../public/2018/500.html' );
		flush();
	}
}