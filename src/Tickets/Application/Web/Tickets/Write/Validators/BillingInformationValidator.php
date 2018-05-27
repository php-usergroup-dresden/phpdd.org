<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class BillingInformationValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isNonEmptyString( 'firstname', ['firstname' => 'Please enter your firstname.'] )
		          ->isNonEmptyString( 'lastname', ['lastname' => 'Please enter your lastname.'] )
		          ->isEmail( 'email', ['email' => 'Please enter a valid email address.'] )
		          ->isEmail( 'emailRepeat', ['emailRepeat' => 'Please repeat your email address.'] )
		          ->isSame(
			          'emailRepeat',
			          $validator->getValue( 'email' ),
			          ['emailRepeat' => 'Please enter the same email address twice.']
		          )
		          ->isNonEmptyString(
			          'streetWithNumber',
			          ['streetWithNumber' => 'Please enter your street with number.']
		          )
		          ->isNonEmptyString( 'zipCode', ['zipCode' => 'Please enter your ZIP code.'] )
		          ->isNonEmptyString( 'city', ['city' => 'Please enter your city.'] )
		          ->isOneStringOf(
			          'countryCode',
			          CountryCodes::ALL_SHORT,
			          ['countryCode' => 'Please select your country.']
		          )
		          ->ifIsNotSame( 'countryCode', CountryCodes::DE_SHORT, 1 )
		          ->ifIsNonEmptyString( 'companyName', 1 )
		          ->isNonEmptyString(
			          'vatNumber',
			          ['vatNumber' => 'As a company located outside of Germany, please enter your VAT number.']
		          );
	}
}