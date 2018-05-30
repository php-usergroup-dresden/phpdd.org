<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Configs;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\DiscountConfigNotFoundException;
use PHPUnit\Framework\TestCase;

final class DiscountsConfigTest extends TestCase
{
	/** @var DiscountsConfig */
	private $discountsConfig;

	protected function setUp() : void
	{
		$configData = [
			'10% PHPUGDD member discount conference' => [
				'description'    => 'As a member of the PHP USERGROUP DRESDEN e.V. you pay 10% less a the conference ticket.',
				'discount'       => -1190,
				'allowedTickets' => [
					'PHPDD18-CT-01',
				],
				'codes'          => ['D87318324E', 'Z61818566Y'],
			],
			'50% discount on conference ticket'      => [
				'description'    => 'As an attendee of a supporting user group, you pay half the price for the conference ticket!',
				'discount'       => -5950,
				'allowedTickets' => [
					'PHPDD18-CT-01',
					'PHPDD18-WS-01',
				],
				'codes'          => ['P95318357E', 'V32618034P'],
			],
		];

		$this->discountsConfig = new DiscountsConfig( $configData );
	}

	protected function tearDown() : void
	{
		$this->discountsConfig = null;
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetDiscountConfigs() : void
	{
		$discountConfigs = $this->discountsConfig->getDiscountConfigs();

		$this->assertInstanceOf( Generator::class, $discountConfigs );
		$this->assertContainsOnlyInstancesOf( DiscountConfig::class, $discountConfigs );
	}

	/**
	 * @param string $ticketId
	 * @param array  $expectedCodes
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider ticketIdForCodesProvider
	 */
	public function testCanGetDiscountCodesForTicketId( string $ticketId, array $expectedCodes ) : void
	{
		$codes = $this->discountsConfig->getDiscountCodesForTicketId( $ticketId );

		$this->assertSame( $expectedCodes, $codes );
	}

	public function ticketIdForCodesProvider() : array
	{
		return [
			[
				'ticketId'      => 'PHPDD18-CT-01',
				'expectedCodes' => [
					'D87318324E',
					'Z61818566Y',
					'P95318357E',
					'V32618034P',
				],
			],
			[
				'ticketId'      => 'PHPDD18-WS-01',
				'expectedCodes' => [
					'P95318357E',
					'V32618034P',
				],
			],
			[
				'ticketId'      => 'PHPDD18-WS-02',
				'expectedCodes' => [],
			],
		];
	}

	/**
	 * @param string $ticketId
	 * @param string $code
	 * @param string $expectedDiscountName
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\DiscountConfigNotFoundException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider ticketIdAndCodeProvider
	 */
	public function testCanGetDiscountConfigByTicketIdAndCode(
		string $ticketId,
		string $code,
		string $expectedDiscountName
	) : void
	{
		$discountConfig = $this->discountsConfig->getDiscountConfigByTicketIdAndCode( $ticketId, $code );

		$this->assertSame( $expectedDiscountName, $discountConfig->getName()->toString() );
	}

	public function ticketIdAndCodeProvider() : array
	{
		return [
			[
				'ticketId'             => 'PHPDD18-CT-01',
				'code'                 => 'Z61818566Y',
				'expectedDiscountName' => '10% PHPUGDD member discount conference',
			],
			[
				'ticketId'             => 'PHPDD18-CT-01',
				'code'                 => 'D87318324E',
				'expectedDiscountName' => '10% PHPUGDD member discount conference',
			],
			[
				'ticketId'             => 'PHPDD18-CT-01',
				'code'                 => 'P95318357E',
				'expectedDiscountName' => '50% discount on conference ticket',
			],
			[
				'ticketId'             => 'PHPDD18-WS-01',
				'code'                 => 'V32618034P',
				'expectedDiscountName' => '50% discount on conference ticket',
			],
		];
	}

	/**
	 * @throws DiscountConfigNotFoundException
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function testThrowsExceptionIfDiscountConfigNotFoundForTicketIdAndCode() : void
	{
		$this->expectException( DiscountConfigNotFoundException::class );
		$this->expectExceptionMessage( 'Could not find discount config.' );

		$this->discountsConfig->getDiscountConfigByTicketIdAndCode( 'PHPDD18-WS-01', 'Z61818566Y' );
	}
}
