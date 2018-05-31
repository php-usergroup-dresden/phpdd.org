<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\PaymentProviderValidator;
use PHPUnit\Framework\TestCase;

final class PaymentProviderValidatorTest extends TestCase
{
	/**
	 * @param string $selection
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider invalidSelectionProvider
	 */
	public function testValidationOfPaymentProviderFailsForInvalidSelection( string $selection ) : void
	{
		$userInput = new UserInput( ['paymentProvider' => $selection] );
		$validator = new PaymentProviderValidator( $userInput );

		$expectedMessages = [
			'paymentProvider' => ['Please select a payment provider.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	public function invalidSelectionProvider() : array
	{
		return [
			[
				'selection' => '',
			],
			[
				'selection' => 'unknown-provider',
			],
		];
	}

	/**
	 * @param string $selection
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider validSelectionProvider
	 */
	public function testValidationOfPaymentProviderPasses( string $selection ) : void
	{
		$userInput = new UserInput( ['paymentProvider' => $selection] );
		$validator = new PaymentProviderValidator( $userInput );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );
	}

	public function validSelectionProvider() : Generator
	{
		foreach ( PaymentProviders::ALL as $provider )
		{
			yield ['selection' => $provider];
		}
	}
}
