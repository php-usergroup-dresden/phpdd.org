<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class StripeSuccessValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isNonEmptyString( 'stripeToken', ['paymentProvider' => 'Invalid response from Stripe received.'] )
		          ->ifPassed( 1 )
		          ->isNonEmptyString( 'stripeEmail', ['paymentProvider' => 'Invalid response from Stripe received.'] );
	}
}