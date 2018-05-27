<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\BillingInformationValidator;
use PHPUnit\Framework\TestCase;

final class BillingInformationValidatorTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsForEmptyInput() : void
	{
		$userInput = new UserInput( [] );
		$validator = new BillingInformationValidator( $userInput );

		$expectedMessages = [
			'firstname'        => ['Please enter your firstname.'],
			'lastname'         => ['Please enter your lastname.'],
			'email'            => ['Please enter a valid email address.'],
			'emailRepeat'      => ['Please repeat your email address.'],
			'streetWithNumber' => ['Please enter your street with number.'],
			'zipCode'          => ['Please enter your ZIP code.'],
			'city'             => ['Please enter your city.'],
			'countryCode'      => ['Please select your country.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsIfACompanyOutsideGermanyDoesNotProvideAVatNumber() : void
	{
		$input = [
			'companyName'      => 'ACME Inc.',
			'firstname'        => 'Unit',
			'lastname'         => 'Tester',
			'email'            => 'unit@test.de',
			'emailRepeat'      => 'unit@test.de',
			'streetWithNumber' => 'Sesamstreet 123',
			'zipCode'          => '98765',
			'city'             => 'Duckcity',
			'countryCode'      => CountryCodes::US_SHORT,
			'vatNumber'        => '',
		];

		$userInput = new UserInput( $input );
		$validator = new BillingInformationValidator( $userInput );

		$expectedMessages = [
			'vatNumber' => ['As a company located outside of Germany, please enter your VAT number.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationFailsIfTheRepeatedEmailIsNotTheSame() : void
	{
		$input = [
			'companyName'      => 'ACME Inc.',
			'firstname'        => 'Unit',
			'lastname'         => 'Tester',
			'email'            => 'unit@test.de',
			'emailRepeat'      => 'test@unit.de',
			'streetWithNumber' => 'Sesamstreet 123',
			'zipCode'          => '98765',
			'city'             => 'Duckcity',
			'countryCode'      => CountryCodes::DE_SHORT,
			'vatNumber'        => '',
		];

		$userInput = new UserInput( $input );
		$validator = new BillingInformationValidator( $userInput );

		$expectedMessages = [
			'emailRepeat' => ['Please enter the same email address twice.'],
		];

		$this->assertTrue( $validator->failed() );
		$this->assertFalse( $validator->passed() );
		$this->assertSame( $expectedMessages, $validator->getMessages() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValidationPasses() : void
	{
		$input = [
			'companyName'      => 'ACME Inc.',
			'firstname'        => 'Unit',
			'lastname'         => 'Tester',
			'email'            => 'unit@test.de',
			'emailRepeat'      => 'unit@test.de',
			'streetWithNumber' => 'Sesamstreet 123',
			'zipCode'          => '98765',
			'city'             => 'Duckcity',
			'countryCode'      => CountryCodes::US_SHORT,
			'vatNumber'        => 'US-12345678',
		];

		$userInput = new UserInput( $input );
		$validator = new BillingInformationValidator( $userInput );

		$this->assertFalse( $validator->failed() );
		$this->assertTrue( $validator->passed() );
		$this->assertSame( [], $validator->getMessages() );
	}
}
