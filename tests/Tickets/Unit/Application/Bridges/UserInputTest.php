<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Bridges;

use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUnit\Framework\TestCase;

final class UserInputTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetValuesTrimmed() : void
	{
		$input = [
			'scalar'   => ' test ',
			'empty'    => ' ',
			'linefeed' => "\n",
			'tab'      => "\t",
			'return'   => "\r",
			'number'   => 123,
			'array'    => [
				'scalar'   => ' test ',
				'empty'    => ' ',
				'linefeed' => "\n",
				'tab'      => "\t",
				'return'   => "\r",
				'number'   => 123,
				'array'    => [
					'scalar'   => ' test ',
					'empty'    => ' ',
					'linefeed' => "\n",
					'tab'      => "\t",
					'return'   => "\r",
					'number'   => 123,
				],
			],
		];

		$userInput = new UserInput( $input );

		$this->assertSame( 'test', $userInput->getValueToValidate( 'scalar' ) );
		$this->assertSame( '', $userInput->getValueToValidate( 'empty' ) );
		$this->assertSame( '', $userInput->getValueToValidate( 'linefeed' ) );
		$this->assertSame( '', $userInput->getValueToValidate( 'tab' ) );
		$this->assertSame( '', $userInput->getValueToValidate( 'return' ) );
		$this->assertSame( 123, $userInput->getValueToValidate( 'number' ) );

		$expectedArray = [
			'scalar'   => 'test',
			'empty'    => '',
			'linefeed' => '',
			'tab'      => '',
			'return'   => '',
			'number'   => 123,
			'array'    => [
				'scalar'   => 'test',
				'empty'    => '',
				'linefeed' => '',
				'tab'      => '',
				'return'   => '',
				'number'   => 123,
			],
		];

		$this->assertSame( $expectedArray, $userInput->getValueToValidate( 'array' ) );
	}
}
