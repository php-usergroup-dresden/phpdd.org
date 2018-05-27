<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class DiscountValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	/** @var string */
	private $discountKey;

	public function __construct( UserInput $userInput, string $ticketId, int $discountIndex )
	{
		parent::__construct( $userInput );
		$this->discountKey = sprintf( 'discounts[%s][%d]', $ticketId, $discountIndex );
	}

	protected function validate( FluidValidator $validator ) : void
	{
		$validator->ifIsNonEmptyString( 'discountCode', 1 )
		          ->matchesRegex(
			          'discountCode',
			          '#^[A-Z]\d{3}18\d{3}[A-Z]$#',
			          [$this->discountKey => 'Code is invalid for this ticket.']
		          );
	}
}