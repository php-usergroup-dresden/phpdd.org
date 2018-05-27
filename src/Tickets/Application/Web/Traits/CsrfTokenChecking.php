<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Traits;

use IceHawk\Forms\Exceptions\InvalidTokenString;
use IceHawk\Forms\Form;
use IceHawk\Forms\Security\Token;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling\SentryClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling\Severity;

trait CsrfTokenChecking
{
	protected function csrfCheckFailed( Form $form, string $inputToken, SentryClient $errorHandler ) : bool
	{
		try
		{
			$token = Token::fromString( $inputToken );
		}
		catch ( InvalidTokenString $e )
		{
			$errorHandler->captureException(
				$e,
				Severity::INFO,
				[
					'formId' => $form->getFormId()->toString(),
				]
			);

			return true;
		}

		return !$form->isTokenValid( $token );
	}
}