<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesDiscountCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class DiscountCodeValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	/** @var string */
	private $ticketId;

	/** @var string */
	private $discountKey;

	/** @var ProvidesDiscountCodes */
	private $discountCodeProvider;

	/** @var array */
	private $redeemedDiscountCodes;

	public function __construct(
		UserInput $userInput,
		ProvidesDiscountCodes $discountCodeProvider,
		array $redeemedDiscountCodes,
		string $ticketId,
		int $discountIndex
	)
	{
		parent::__construct( $userInput );
		$this->ticketId              = $ticketId;
		$this->discountCodeProvider  = $discountCodeProvider;
		$this->redeemedDiscountCodes = $redeemedDiscountCodes;
		$this->discountKey           = sprintf( 'discounts[%s][%d]', $ticketId, $discountIndex );
	}

	protected function validate( FluidValidator $validator ) : void
	{
		$availableDiscountCodes = array_diff(
			$this->discountCodeProvider->getDiscountCodesForTicketId( $this->ticketId ),
			$this->redeemedDiscountCodes
		);

		$validator->ifIsNonEmptyString( 'discountCode', 2 )
		          ->matchesRegex(
			          'discountCode',
			          '#^[A-Z]\d{3}18\d{3}[A-Z]$#',
			          [$this->discountKey => 'Code is invalid for this ticket.']
		          )
		          ->ifPassed( 1 )
		          ->isOneStringOf(
			          'discountCode',
			          $availableDiscountCodes,
			          [$this->discountKey => 'Code is invalid for this ticket.']
		          );
	}
}