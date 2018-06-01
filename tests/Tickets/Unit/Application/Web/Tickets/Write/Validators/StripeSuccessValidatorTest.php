<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\StripeSuccessValidator;
use PHPUnit\Framework\TestCase;

final class StripeSuccessValidatorTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsIfStripeTokenIsEmptyOrNull() : void
	{
		$userInput = new UserInput( ['stripeToken' => null] );
		$validator = new StripeSuccessValidator( $userInput );

		$expectedMessages = [
			'paymentProvider' => ['Invalid response from Stripe received.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );

		$userInput = new UserInput( ['stripeToken' => ''] );
		$validator = new StripeSuccessValidator( $userInput );

		$expectedMessages = [
			'paymentProvider' => ['Invalid response from Stripe received.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsIfStripeEmailIsEmptyOrNull() : void
	{
		$userInput = new UserInput( ['stripeEmail' => null] );
		$validator = new StripeSuccessValidator( $userInput );

		$expectedMessages = [
			'paymentProvider' => ['Invalid response from Stripe received.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );

		$userInput = new UserInput( ['stripeEmail' => ''] );
		$validator = new StripeSuccessValidator( $userInput );

		$expectedMessages = [
			'paymentProvider' => ['Invalid response from Stripe received.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}
}
