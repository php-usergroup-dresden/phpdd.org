<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUnit\Framework\TestCase;

final class PaymentProviderTest extends TestCase
{
	public function testThrowsExceptionWhenConstructedWithInvalidPaymentProvider() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid payment provider given.' );

		new PaymentProvider( 'unknown' );
	}

	/**
	 * @param string $provider
	 *
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider validPaymentProvider
	 */
	public function testCanConstructFromValidPaymentProvider( string $provider ) : void
	{
		$paymentProvider = new PaymentProvider( $provider );

		$this->assertInstanceOf( PaymentProvider::class, $paymentProvider );
	}

	public function validPaymentProvider() : Generator
	{
		foreach ( PaymentProviders::ALL as $provider )
		{
			yield ['provider' => $provider];
		}
	}
}
