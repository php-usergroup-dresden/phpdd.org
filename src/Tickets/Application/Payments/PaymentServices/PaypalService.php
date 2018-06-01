<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\PaysTicketOrders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentAuthorizationResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data\PaypalAddress;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data\PaypalCart;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Data\PaypalCartItem;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\PaypalAuthorizeRequest;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\PaypalClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\PaypalExecuteRequest;
use Throwable;
use function sprintf;

final class PaypalService implements PaysTicketOrders
{
	/** @var PaypalClient */
	private $paypalClient;

	public function __construct( PaypalClient $paypalClient )
	{
		$this->paypalClient = $paypalClient;
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws RuntimeException
	 * @throws \InvalidArgumentException
	 * @throws InvalidArgumentException
	 * @return ProvidesPaymentAuthorizationResult
	 */
	public function authorize( TicketOrder $ticketOrder ) : ProvidesPaymentAuthorizationResult
	{
		$decimalFormatter          = new DecimalMoneyFormatter( new ISOCurrencies() );
		$ticketOrderBillingAddress = $ticketOrder->getBillingAddress();
		if ( null === $ticketOrderBillingAddress )
		{
			throw new RuntimeException( 'No billing address set in ticket order.' );
		}

		$billingAddress = new PaypalAddress(
			$ticketOrderBillingAddress->getFirstname()->toString(),
			$ticketOrderBillingAddress->getLastname()->toString(),
			(null !== $ticketOrderBillingAddress->getAddressAddon())
				? $ticketOrderBillingAddress->getAddressAddon()->toString()
				: '',
			$ticketOrderBillingAddress->getStreetWithNumber()->toString(),
			$ticketOrderBillingAddress->getZipCode()->toString(),
			$ticketOrderBillingAddress->getCity()->toString(),
			$ticketOrderBillingAddress->getCountryCode()->toString()
		);

		$shippingAddress = clone $billingAddress;

		$cart = new PaypalCart(
			$decimalFormatter->format( $ticketOrder->getPaymentTotal()->getMoney() ),
			$ticketOrder->getPaymentTotal()->getMoney()->getCurrency()->getCode(),
			'Your ticket order for PHP Developer Days 2018 in Dresden, Germany'
		);

		foreach ( $ticketOrder->getTicketItems() as $ticketItem )
		{
			$cartItem = new PaypalCartItem(
				sprintf(
					'%s: %s',
					$ticketItem->getAttendeeName()->toString(),
					$ticketItem->getTicket()->getName()->toString()
				),
				$ticketItem->getTicket()->getDescription()->toString(),
				1,
				$decimalFormatter->format( $ticketItem->getTicket()->getPrice()->getMoney() ),
				$ticketItem->getTicket()->getPrice()->getMoney()->getCurrency()->getCode()
			);

			$cart->addCartItem( $cartItem );
		}

		$discountCartItem = new PaypalCartItem(
			'Discounts', 'Sum of all ticket discounts',
			1,
			$decimalFormatter->format( $ticketOrder->getDiscountTotal()->getMoney() ),
			$ticketOrder->getDiscountTotal()->getMoney()->getCurrency()->getCode()
		);

		$cart->addCartItem( $discountCartItem );

		$diversityDonation = $ticketOrder->getDiversityDonation();
		if ( null !== $diversityDonation )
		{
			$diversityDonationCartItem = new PaypalCartItem(
				'Diversity donation', 'Your contribution to more diversity at our event.',
				1,
				$decimalFormatter->format( $diversityDonation->getMoney() ),
				$diversityDonation->getMoney()->getCurrency()->getCode()
			);

			$cart->addCartItem( $diversityDonationCartItem );
		}

		$paymentFee         = $ticketOrder->getPaymentFee();
		$paymentFeeCartItem = new PaypalCartItem(
			'Payment fee',
			'Payment fee',
			1,
			$decimalFormatter->format( $paymentFee->getMoney() ),
			$paymentFee->getMoney()->getCurrency()->getCode()
		);

		$cart->addCartItem( $paymentFeeCartItem );

		$authRequest = new PaypalAuthorizeRequest(
			$ticketOrder->getOrderId()->toString(),
			$shippingAddress,
			$billingAddress,
			$cart
		);

		try
		{
			$payment = $this->paypalClient->authorizePayment( $authRequest );

			return PaymentAuthorizationResult::fromPaypalPayment( $payment );
		}
		catch ( Throwable $e )
		{
			return new PaymentAuthorizationResult( ResultType::FAILED, $e->getMessage() );
		}
	}

	/**
	 * @param PaymentId $paymentId
	 * @param PayerId   $payerId
	 *
	 * @return ProvidesPaymentExecutionResult
	 */
	public function execute( PaymentId $paymentId, PayerId $payerId ) : ProvidesPaymentExecutionResult
	{
		$execRequest = new PaypalExecuteRequest( $paymentId->toString(), $payerId->toString() );

		try
		{
			$payment = $this->paypalClient->executePayment( $execRequest );

			return PaymentExecutionResult::fromPaypalPayment( $payment );
		}
		catch ( Throwable $e )
		{
			return new PaymentExecutionResult( ResultType::FAILED, $e->getMessage() );
		}
	}
}