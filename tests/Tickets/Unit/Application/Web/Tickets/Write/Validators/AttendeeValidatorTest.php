<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\AttendeeValidator;
use PHPUnit\Framework\TestCase;

final class AttendeeValidatorTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsForEmptyAttendeeName() : void
	{
		$userInput = new UserInput( [] );
		$validator = new AttendeeValidator( $userInput, 'PHPDD18-CT-01', 0 );

		$expectedMessages = [
			'attendees[PHPDD18-CT-01][0]' => [
				'Please enter the name of an attendee.',
			],
		];

		$this->assertFalse( $validator->passed() );
		$this->assertTrue( $validator->failed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );

		$userInput = new UserInput( ['attendeeName' => ' '] );
		$validator = new AttendeeValidator( $userInput, 'PHPDD18-CT-01', 0 );

		$expectedMessages = [
			'attendees[PHPDD18-CT-01][0]' => [
				'Please enter the name of an attendee.',
			],
		];

		$this->assertFalse( $validator->passed() );
		$this->assertTrue( $validator->failed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationPasses() : void
	{
		$userInput = new UserInput( ['attendeeName' => 'John Doe'] );
		$validator = new AttendeeValidator( $userInput, 'PHPDD18-CT-01', 0 );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );
	}
}
