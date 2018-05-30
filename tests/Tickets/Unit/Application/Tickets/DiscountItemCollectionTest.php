<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItemCollection;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class DiscountItemCollectionTest extends TestCase
{
	use DiscountItemProviding;
	use MoneyProviding;

	/** @var DiscountItemCollection */
	private $discountItemCollection;

	/** @var DiscountItem */
	private $discountItemOne;

	/** @var DiscountItem */
	private $discountItemTwo;

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	protected function setUp() : void
	{
		$this->discountItemOne = $this->getDiscountItem(
			'Discount One',
			'D87318324E',
			'Discount Description One',
			$this->getMoney( -1000 ),
			[]
		);

		$this->discountItemTwo = $this->getDiscountItem(
			'Discount Two',
			'P95318357E',
			'Discount Description Two',
			$this->getMoney( -2000 ),
			[]
		);

		$this->discountItemCollection = new DiscountItemCollection();
		$this->discountItemCollection->add( $this->discountItemOne );
		$this->discountItemCollection->add( $this->discountItemTwo );
	}

	protected function tearDown() : void
	{
		$this->discountItemCollection = null;
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanRewindCollection() : void
	{
		$this->discountItemCollection->rewind();
		$this->discountItemCollection->next();

		$this->assertSame( $this->discountItemTwo, $this->discountItemCollection->current() );

		$this->discountItemCollection->rewind();

		$this->assertSame( $this->discountItemOne, $this->discountItemCollection->current() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCurrent() : void
	{
		$this->assertSame( $this->discountItemOne, $this->discountItemCollection->current() );

		$this->discountItemCollection->next();

		$this->assertSame( $this->discountItemTwo, $this->discountItemCollection->current() );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 */
	public function testAdd() : void
	{
		$discountItemThree = $this->getDiscountItem(
			'Discount Three',
			'D87318324E',
			'Discount Description Three',
			$this->getMoney( -3000 ),
			[]
		);

		$this->discountItemCollection->add( $discountItemThree );

		$this->assertCount( 3, $this->discountItemCollection );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testKey() : void
	{
		$this->assertSame( 0, $this->discountItemCollection->key() );

		$this->discountItemCollection->next();

		$this->assertSame( 1, $this->discountItemCollection->key() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testValid() : void
	{
		$this->discountItemCollection->next();
		$this->discountItemCollection->next();

		$this->assertFalse( $this->discountItemCollection->valid() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCount() : void
	{
		$this->assertSame( 2, $this->discountItemCollection->count() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testNext() : void
	{
		$this->discountItemCollection->next();
		$this->discountItemCollection->next();

		$this->assertFalse( $this->discountItemCollection->valid() );
	}
}
