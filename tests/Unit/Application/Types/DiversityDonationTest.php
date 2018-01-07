<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class DiversityDonationTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class DiversityDonationTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @param Money $money
	 *
	 * @throws \PHPUnit\Framework\Exception
	 * @dataProvider validMoneyProvider
	 */
	public function testCanCreateInstanceFromValidMoney( Money $money ) : void
	{
		$diversityDonation = new DiversityDonation( $money );

		$this->assertInstanceOf( DiversityDonation::class, $diversityDonation );
		$this->assertSame( $money, $diversityDonation->getMoney() );
	}

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function validMoneyProvider() : array
	{
		return [
			[
				'money' => $this->getMoney( 0 ),
			],
			[
				'money' => $this->getMoney( 1 ),
			],
			[
				'money' => $this->getMoney( 1000 ),
			],
		];
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws \InvalidArgumentException
	 */
	public function testThrowsExceptionForNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new DiversityDonation( $this->getMoney( -1000 ) );
	}
}
