<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class PaymentProviderValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isOneStringOf(
			'paymentProvider',
			PaymentProviders::ALL,
			['paymentProvider' => 'Please select a payment provider.']
		);
	}
}