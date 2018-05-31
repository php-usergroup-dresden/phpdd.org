<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal;

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Exceptions\ExecutionOfPaypalPaymentFailed;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Interfaces\ConfiguresPaypalClient;

final class PaypalClient
{
	/** @var ConfiguresPaypalClient */
	private $config;

	public function __construct( ConfiguresPaypalClient $config )
	{
		$this->config = $config;
	}

	/**
	 * @param PaypalAuthorizeRequest $authorizeRequest
	 *
	 * @throws \InvalidArgumentException
	 * @return Payment
	 */
	public function authorizePayment( PaypalAuthorizeRequest $authorizeRequest ) : Payment
	{
		$apiContext = $this->getApiContext();

		$paypalShippingAddress = new ShippingAddress();
		$paypalShippingAddress->setRecipientName(
			sprintf(
				'%s %s',
				$authorizeRequest->getShippingAddress()->getFirstname(),
				$authorizeRequest->getShippingAddress()->getLastname()
			)
		);
		$paypalShippingAddress->setLine1(
			$authorizeRequest->getShippingAddress()->getStreetWithNumber()
		);
		$paypalShippingAddress->setLine2( $authorizeRequest->getShippingAddress()->getAddon() );
		$paypalShippingAddress->setPostalCode( $authorizeRequest->getShippingAddress()->getZipCode() );
		$paypalShippingAddress->setCity( $authorizeRequest->getShippingAddress()->getCity() );
		$paypalShippingAddress->setCountryCode( $authorizeRequest->getShippingAddress()->getCountryCode() );

		$payer = new Payer();
		$payer->setPaymentMethod( 'paypal' );

		$amount = new Amount();
		$amount->setCurrency( $authorizeRequest->getCart()->getCurrencyCode() );
		$amount->setTotal( $authorizeRequest->getCart()->getCartTotal() );

		$itemList = new ItemList();
		$itemList->setShippingAddress( $paypalShippingAddress );

		foreach ( $authorizeRequest->getCart()->getCartItems() as $cartItem )
		{
			$item = new Item();
			$item->setName( $cartItem->getName() );
			$item->setQuantity( $cartItem->getQuantity() );
			$item->setCurrency( $cartItem->getCurrencyCode() );
			$item->setPrice( $cartItem->getPrice() );
			$item->setDescription( $cartItem->getDescription() );

			$itemList->addItem( $item );
		}

		$transaction = new Transaction();
		$transaction->setAmount( $amount );
		$transaction->setDescription( $authorizeRequest->getCart()->getDescription() );
		$transaction->setItemList( $itemList );
		$transaction->setInvoiceNumber( $authorizeRequest->getOrderId() );

		$payment = new Payment();
		$payment->setIntent( 'authorize' );
		$payment->setPayer( $payer );
		$payment->setTransactions( [$transaction] );

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setCancelUrl( $this->config->getCancelUrl() );
		$redirectUrls->setReturnUrl( $this->config->getSuccessUrl() );

		$payment->setRedirectUrls( $redirectUrls );

		return $payment->create( $apiContext );
	}

	/**
	 * @param PaypalExecuteRequest $executeRequest
	 *
	 * @throws ExecutionOfPaypalPaymentFailed
	 * @return Payment
	 * @throws \InvalidArgumentException
	 */
	public function executePayment( PaypalExecuteRequest $executeRequest ) : Payment
	{
		$apiContext = $this->getApiContext();
		$payment    = Payment::get( $executeRequest->getPaymentId(), $apiContext );
		$execution  = new PaymentExecution();
		$execution->setPayerId( $executeRequest->getPayerId() );

		try
		{
			return $payment->execute( $execution, $apiContext );
		}
		catch ( \Exception $e )
		{
			throw (new ExecutionOfPaypalPaymentFailed( $e->getMessage(), 0, $e ))->withPayment( $payment );
		}
	}

	private function getApiContext() : ApiContext
	{
		$apiContext = new ApiContext(
			new OAuthTokenCredential( $this->config->getClientId(), $this->config->getClientSecret() )
		);

		$apiContext->setConfig(
			[
				'mode' => $this->config->getAuthEndpoint() === 'production' ? 'live' : 'sandbox',
			]
		);

		return $apiContext;
	}
}
