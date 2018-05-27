<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use hollodotme\FluidValidator\FluidValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractUserInputValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;

final class AttendeeValidator extends AbstractUserInputValidator implements ValidatesUserInput
{
	/** @var string */
	private $attendeeKey;

	public function __construct( UserInput $userInput, string $ticketId, int $attendeeIndex )
	{
		parent::__construct( $userInput );
		$this->attendeeKey = sprintf( 'attendees[%s][%d]', $ticketId, $attendeeIndex );
	}

	protected function validate( FluidValidator $validator ) : void
	{
		$validator->isNonEmptyString(
			'attendeeName',
			[$this->attendeeKey => 'Please enter the name of an attendee.']
		);
	}
}