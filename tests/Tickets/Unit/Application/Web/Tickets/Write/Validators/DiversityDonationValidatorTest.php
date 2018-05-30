<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\DiversityDonationValidator;
use PHPUnit\Framework\TestCase;

final class DiversityDonationValidatorTest extends TestCase
{
	/**
	 * @param int $amount
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider outOfRangeDonationProvider
	 */
	public function testValidationOfDiversityDonationFailsIfAmountIsOutOfRange( int $amount ) : void
	{
		$userInput = new UserInput( ['diversityDonation' => $amount] );
		$validator = new DiversityDonationValidator( $userInput );

		$expectedMessages = [
			'diversityDonation' => ['Please enter a donation between €0 and €800.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	public function outOfRangeDonationProvider() : array
	{
		return [
			[
				'amount' => -1,
			],
			[
				'amount' => 801,
			],
		];
	}
}
