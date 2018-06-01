<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories;

use DateTimeImmutable;
use PDO;
use PDOException;
use PDOStatement;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesRedeemedDiscountCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketItemId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use Throwable;
use function json_encode;

final class TicketOrderRepository implements ProvidesReservedTicketCount, ProvidesRedeemedDiscountCodes
{
	/** @var PDO */
	private $database;

	public function __construct( PDO $database )
	{
		$this->database = $database;
	}

	/**
	 * @param Ticket $ticket
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	public function getReservedCount( Ticket $ticket ) : int
	{
		$query = 'SELECT COUNT(1) FROM `ticketOrderItems` WHERE ticketId = :ticketId';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'ticketId' => $ticket->getId()->toString(),
			]
		);

		$this->guardStatementSucceeded( $statement );

		return (int)$statement->fetchColumn();
	}

	/**
	 * @param PDOStatement $statement
	 *
	 * @throws RuntimeException
	 */
	private function guardStatementSucceeded( PDOStatement $statement ) : void
	{
		if ( '00000' !== $statement->errorCode() )
		{
			$errorInfo = $statement->errorInfo();
			throw new RuntimeException(
				sprintf( 'Database error: "%s" (Code: %s)', $errorInfo[2] ?? '', $errorInfo[0] ?? '' )
			);
		}
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function placeTicketOrder( TicketOrder $ticketOrder ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$this->addTicketOrderRecord( $ticketOrder );
			$this->addTicketOrderAddressRecord( $ticketOrder );
			$this->addTicketOrderItemRecords( $ticketOrder );
			$this->addTicketOrderPayment( $ticketOrder );

			$this->database->commit();
		}
		catch ( Throwable $e )
		{
			$this->database->rollBack();

			throw $e;
		}
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws \InvalidArgumentException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	private function addTicketOrderRecord( TicketOrder $ticketOrder ) : void
	{
		$query = 'INSERT INTO `ticketOrders` (orderId, date, email, paymentProvider, currencyCode, orderTotal, discountTotal, diversityDonation, paymentFee, paymentTotal) 
			VALUES (:orderId, :date, :email, :paymentProvider, :currencyCode, :orderTotal, :discountTotal, :diversityDonation, :paymentFee, :paymentTotal)';

		$emailAddress = $ticketOrder->getEmailAddress();
		if ( null === $emailAddress )
		{
			throw new RuntimeException( 'No email address set in ticket order.' );
		}

		$paymentProvider = $ticketOrder->getPaymentProvider();
		if ( null === $paymentProvider )
		{
			throw new RuntimeException( 'No payment provider set in ticket order.' );
		}

		$diversityDonation = $ticketOrder->getDiversityDonation();

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId'           => $ticketOrder->getOrderId()->toString(),
				'date'              => $ticketOrder->getOrderDate()->format( 'c' ),
				'email'             => $emailAddress->toString(),
				'paymentProvider'   => $paymentProvider->toString(),
				'currencyCode'      => $ticketOrder->getOrderTotal()->getMoney()->getCurrency()->getCode(),
				'orderTotal'        => $ticketOrder->getOrderTotal()->getMoney()->getAmount(),
				'discountTotal'     => $ticketOrder->getDiscountTotal()->getMoney()->getAmount(),
				'diversityDonation' => (null !== $diversityDonation)
					? $diversityDonation->getMoney()->getAmount()
					: '0',
				'paymentFee'        => $ticketOrder->getPaymentFee()->getMoney()->getAmount(),
				'paymentTotal'      => $ticketOrder->getPaymentTotal()->getMoney()->getAmount(),
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws RuntimeException
	 */
	private function addTicketOrderAddressRecord( TicketOrder $ticketOrder ) : void
	{
		$billingAddress = $ticketOrder->getBillingAddress();

		if ( null === $billingAddress )
		{
			throw new RuntimeException( 'No billing address set in ticket order.' );
		}

		$query = 'INSERT INTO `ticketOrderAddresses` (orderId, companyName, firstname, lastname, streetWithNumber, addressAddon, zipCode, city, countryCode, vatNumber) 
				  VALUES (:orderId, :companyName, :firstname, :lastname, :streetWithNumber, :addressAddon, :zipCode, :city, :countryCode, :vatNumber)';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId'          => $ticketOrder->getOrderId()->toString(),
				'companyName'      => (null !== $billingAddress->getCompanyName())
					? $billingAddress->getCompanyName()->toString()
					: null,
				'firstname'        => $billingAddress->getFirstname()->toString(),
				'lastname'         => $billingAddress->getLastname()->toString(),
				'streetWithNumber' => $billingAddress->getStreetWithNumber()->toString(),
				'addressAddon'     => (null !== $billingAddress->getAddressAddon())
					? $billingAddress->getAddressAddon()->toString()
					: null,
				'zipCode'          => $billingAddress->getZipCode()->toString(),
				'city'             => $billingAddress->getCity()->toString(),
				'countryCode'      => $billingAddress->getCountryCode()->toString(),
				'vatNumber'        => (null !== $billingAddress->getVatNumber())
					? $billingAddress->getVatNumber()->toString() #
					: null,
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws RuntimeException
	 */
	private function addTicketOrderItemRecords( TicketOrder $ticketOrder ) : void
	{
		foreach ( $ticketOrder->getTicketItems() as $ticketItem )
		{
			$this->addTicketOrderItemRecord( $ticketOrder->getOrderId(), $ticketItem );
		}
	}

	/**
	 * @param TicketOrderId $ticketOrderId
	 * @param TicketItem    $ticketItem
	 *
	 * @throws RuntimeException
	 */
	private function addTicketOrderItemRecord( TicketOrderId $ticketOrderId, TicketItem $ticketItem ) : void
	{
		$query = 'INSERT INTO `ticketOrderItems` (itemId, orderId, ticketId, attendeeName, discountCode) 
				  VALUES (:itemId, :orderId, :ticketId, :attendeeName, :discountCode)';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'itemId'       => TicketItemId::generate()->toString(),
				'orderId'      => $ticketOrderId->toString(),
				'ticketId'     => $ticketItem->getTicket()->getId()->toString(),
				'attendeeName' => $ticketItem->getAttendeeName()->toString(),
				'discountCode' => (null !== $ticketItem->getDiscountItem())
					? $ticketItem->getDiscountItem()->getCode()->toString()
					: null,
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws RuntimeException
	 */
	private function addTicketOrderPayment( TicketOrder $ticketOrder ) : void
	{
		$payment = $ticketOrder->getPayment();

		if ( null === $payment )
		{
			throw new RuntimeException( 'No payment set in ticket order.' );
		}

		$query = 'INSERT INTO `ticketOrderPayments` (paymentId, orderId, payerId, metaData, `status`, executedAt) 
				  VALUES (:paymentId, :orderId, :payerId, :metaData, :status, :executedAt)';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'paymentId'  => $payment->getPaymentId()->toString(),
				'orderId'    => $ticketOrder->getOrderId()->toString(),
				'payerId'    => $payment->getPayerId()->toString(),
				'metaData'   => json_encode( $payment->getMetaData() ),
				'status'     => 'pending',
				'executedAt' => null,
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param PaymentId         $paymentId
	 * @param DateTimeImmutable $executedAt
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function markPaymentAsExecuted( PaymentId $paymentId, DateTimeImmutable $executedAt ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$query = 'UPDATE `ticketOrderPayments` 
					  SET `status` = :status, 
					  	  `executedAt` = :executedAt 
					  WHERE paymentId = :paymentId LIMIT 1';

			$statement = $this->database->prepare( $query );
			$statement->execute(
				[
					'status'     => 'executed',
					'paymentId'  => $paymentId->toString(),
					'executedAt' => $executedAt->format( 'c' ),
				]
			);

			$this->guardStatementSucceeded( $statement );

			$this->database->commit();
		}
		catch ( Throwable $e )
		{
			$this->database->rollBack();

			throw $e;
		}
	}

	/**
	 * @throws RuntimeException
	 * @return array
	 */
	public function getRedeemedDiscountCodes() : array
	{
		$query     = 'SELECT DISTINCT `discountCode` FROM `ticketOrderItems` WHERE discountCode IS NOT NULL';
		$statement = $this->database->query( $query );

		$this->guardStatementSucceeded( $statement );

		return (array)$statement->fetchAll( PDO::FETCH_COLUMN, 0 );
	}
}