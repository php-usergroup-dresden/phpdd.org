<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class PurchaseOptInsValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isEqual(
			'cocOptIn',
			'1',
			['general' => 'Please confirm that you read our Code of Conduct and agree to its enforcement.']
		);
	}
}