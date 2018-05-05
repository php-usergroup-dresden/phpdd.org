<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\FinalResponders;

use IceHawk\IceHawk\Constants\HttpCode;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use PHPUGDD\PHPDD\Website\Tickets\Traits\InfrastructureInjecting;

/**
 * Class FinalReadResponder
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\FinalResponders
 */
final class FinalReadResponder implements RespondsFinallyToReadRequest
{
	use InfrastructureInjecting;

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request ) : void
	{
		try
		{
			/** @var UnresolvedRequest $throwable */
			throw $throwable;
		}
		catch ( UnresolvedRequest $e )
		{
			header( 'Content-Type: text/html; charset=utf-8', true, HttpCode::NOT_FOUND );
			readfile( __DIR__ . '/../../../../public/2018/404.html' );
			flush();
		}
		catch ( \Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );

			header( 'Content-Type: text/html; charset=utf-8', true, HttpCode::INTERNAL_SERVER_ERROR );
			readfile( __DIR__ . '/../../../../public/2018/500.html' );
			flush();
		}
	}
}
