<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class DiversityDonationValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isIntInRange(
			'diversityDonation',
			range( 0, 800 ),
			['diversityDonation' => 'Please enter a donation between €0 and €800.']
		);
	}
}