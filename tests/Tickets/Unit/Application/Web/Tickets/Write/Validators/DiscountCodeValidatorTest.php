<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesDiscountCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\DiscountCodeValidator;
use PHPUnit\Framework\TestCase;

final class DiscountCodeValidatorTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsForInvalidDiscountCode() : void
	{
		$discountCodeProviderStub = $this->getMockBuilder( ProvidesDiscountCodes::class )
		                                 ->getMockForAbstractClass();
		$discountCodeProviderStub->method( 'getDiscountCodesForTicketId' )
		                         ->with( $this->anything() )
		                         ->willReturn( [] );

		/** @var ProvidesDiscountCodes $discountCodeProviderStub */

		$userInput = new UserInput( ['discountCode' => '1234567890'] );
		$validator = new DiscountCodeValidator( $userInput, $discountCodeProviderStub, [], 'PHPDD18-CT-01', 0 );

		$expectedMessages = [
			'discounts[PHPDD18-CT-01][0]' => [
				'Code is invalid for this ticket.',
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
		$discountCodeProviderStub = $this->getMockBuilder( ProvidesDiscountCodes::class )
		                                 ->getMockForAbstractClass();
		$discountCodeProviderStub->method( 'getDiscountCodesForTicketId' )
		                         ->with( 'PHPDD18-CT-01' )
		                         ->willReturn( ['M70918400A'] );

		/** @var ProvidesDiscountCodes $discountCodeProviderStub */

		$userInput = new UserInput( ['discountCode' => 'M70918400A'] );
		$validator = new DiscountCodeValidator( $userInput, $discountCodeProviderStub, [], 'PHPDD18-CT-01', 0 );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );

		$userInput = new UserInput( ['discountCode' => ''] );
		$validator = new DiscountCodeValidator( $userInput, $discountCodeProviderStub, [], 'PHPDD18-CT-01', 0 );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );
	}
}
