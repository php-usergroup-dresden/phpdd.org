<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class PaypalSuccessValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isNonEmptyString( 'paymentId', ['paymentProvider' => 'Invalid response from PayPal received.'] )
		          ->isNonEmptyString( 'token', ['paymentProvider' => 'Invalid response from PayPal received.'] )
		          ->isNonEmptyString( 'PayerID', ['paymentProvider' => 'Invalid response from PayPal received.'] );
	}
}