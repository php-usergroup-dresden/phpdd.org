<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories;

use DateTimeImmutable;
use PDO;
use PDOException;
use PDOStatement;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Invoices\Invoice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountCodesAlreadyRedeemedException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesRedeemedDiscountCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketSaleSummary;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketItemId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces\ProvidesSummaryArray;
use stdClass;
use Throwable;
use function array_merge;
use function array_unique;
use function count;
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
	 * @throws DiscountCodesAlreadyRedeemedException
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function placeTicketOrder( TicketOrder $ticketOrder ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$this->guardDiscountsWereNotRedeemedYet( $ticketOrder );

			$this->addTicketOrderRecord( $ticketOrder );
			$this->addTicketOrderAddressRecord( $ticketOrder );
			$this->addTicketOrderItemRecords( $ticketOrder );
			$this->addTicketOrderPayment( $ticketOrder );

			$this->database->commit();
		}
		catch ( DiscountCodesAlreadyRedeemedException | Throwable $e )
		{
			$this->database->rollBack();

			throw $e;
		}
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws DiscountCodesAlreadyRedeemedException
	 * @throws RuntimeException
	 */
	private function guardDiscountsWereNotRedeemedYet( TicketOrder $ticketOrder ) : void
	{
		$redeemedDiscountCodes = $this->getRedeemedDiscountCodes();
		$discountItems         = $ticketOrder->getDiscountItems();
		$discountCodes         = [];

		foreach ( $discountItems as $discountItem )
		{
			$discountCodes[] = $discountItem->getCode()->toString();
		}

		$alreadyRedeemedCodes = array_unique( array_intersect( $discountCodes, $redeemedDiscountCodes ) );

		if ( 0 !== count( $alreadyRedeemedCodes ) )
		{
			throw new DiscountCodesAlreadyRedeemedException(
				'The following discount codes were already used. Please remove them from your order: '
				. implode( ', ', $alreadyRedeemedCodes )
			);
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
		$query = 'INSERT INTO `ticketOrders` (orderId, date, email, paymentProvider, currencyCode, orderTotal, discountTotal, diversityDonation, paymentFee, paymentTotal, refundTotal) 
			VALUES (:orderId, :date, :email, :paymentProvider, :currencyCode, :orderTotal, :discountTotal, :diversityDonation, :paymentFee, :paymentTotal, 0)';

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
		$query = 'INSERT INTO `ticketOrderItems` (itemId, orderId, ticketId, attendeeName, discountCode, status) 
				  VALUES (:itemId, :orderId, :ticketId, :attendeeName, :discountCode, :status)';

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
				'status'       => $ticketItem->getStatus(),
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param TicketOrder $ticketOrder
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 * @throws \InvalidArgumentException
	 */
	private function addTicketOrderPayment( TicketOrder $ticketOrder ) : void
	{
		$payment = $ticketOrder->getPayment();

		if ( null === $payment )
		{
			throw new RuntimeException( 'No payment set in ticket order.' );
		}

		$query = 'INSERT INTO `ticketOrderPayments` (paymentId, orderId, provider, payerId, metaData, amount, fee, `status`, executedAt) 
				  VALUES (:paymentId, :orderId, :provider, :payerId, :metaData, :amount, :fee, :status, :executedAt)';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'paymentId'  => $payment->getPaymentId()->toString(),
				'orderId'    => $ticketOrder->getOrderId()->toString(),
				'provider'   => $payment->getPaymentProvider()->toString(),
				'payerId'    => $payment->getPayerId()->toString(),
				'metaData'   => json_encode( $payment->getMetaData() ),
				'amount'     => $ticketOrder->getPaymentTotal()->getMoney()->getAmount(),
				'fee'        => $ticketOrder->getPaymentFee()->getMoney()->getAmount(),
				'status'     => 'pending',
				'executedAt' => null,
			]
		);

		$this->guardStatementSucceeded( $statement );
	}

	/**
	 * @param PaymentId         $paymentId
	 * @param DateTimeImmutable $executedAt
	 * @param array             $metaData
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function markPaymentAsExecuted(
		PaymentId $paymentId,
		DateTimeImmutable $executedAt,
		array $metaData = []
	) : void
	{
		$this->database->beginTransaction();

		try
		{
			$query = 'UPDATE `ticketOrderPayments` 
					  SET `status` = :status, 
					  	  `executedAt` = :executedAt,
					  	  `metaData` = IFNULL(:metaData, `metaData`)
					  WHERE paymentId = :paymentId LIMIT 1';

			$statement = $this->database->prepare( $query );
			$statement->execute(
				[
					'status'     => 'executed',
					'paymentId'  => $paymentId->toString(),
					'executedAt' => $executedAt->format( 'c' ),
					'metaData'   => !empty( $metaData ) ? json_encode( $metaData ) : null,
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

	/**
	 * @param TicketOrderId $ticketOrderId
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function removeTicketOrder( TicketOrderId $ticketOrderId ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$params    = ['orderId' => $ticketOrderId->toString()];
			$statement = $this->database->prepare(
				'DELETE FROM `ticketOrderPayments` WHERE `orderId` = :orderId LIMIT 1'
			);
			$statement->execute( $params );

			$this->guardStatementSucceeded( $statement );

			$statement = $this->database->prepare(
				'DELETE FROM `ticketOrderItems` WHERE `orderId` = :orderId LIMIT 1'
			);
			$statement->execute( $params );

			$this->guardStatementSucceeded( $statement );

			$statement = $this->database->prepare(
				'DELETE FROM `ticketOrderAddresses` WHERE `orderId` = :orderId LIMIT 1'
			);
			$statement->execute( $params );

			$this->guardStatementSucceeded( $statement );

			$statement = $this->database->prepare(
				'DELETE FROM `ticketOrders` WHERE `orderId` = :orderId LIMIT 1'
			);
			$statement->execute( $params );

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
	public function getTicketOrderIdsNotHavingEmailsSent() : array
	{
		$query = 'SELECT DISTINCT `to`.`orderId` 
				  FROM `ticketOrders` AS `to` 
				  LEFT JOIN `ticketOrderMails` AS `tom` USING(`orderId`)
				  WHERE `tom`.orderId IS NULL
				  ORDER BY `to`.`date` ASC
				  LIMIT 10';

		$statement = $this->database->query( $query );

		$this->guardStatementSucceeded( $statement );

		return (array)$statement->fetchAll( PDO::FETCH_COLUMN, 0 );
	}

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return stdClass
	 */
	public function getTicketOrderRecord( string $ticketOrderId ) : stdClass
	{
		$query     = 'SELECT * FROM `ticketOrders` WHERE orderId = :orderId LIMIT 1';
		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId' => $ticketOrderId,
			]
		);

		$this->guardStatementSucceeded( $statement );

		$ticketOrder = $statement->fetchObject();

		if ( false === $ticketOrder )
		{
			throw new RuntimeException( 'Ticket order not found: ' . $ticketOrderId );
		}

		return $ticketOrder;
	}

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return stdClass
	 */
	public function getTicketOrderAddressRecord( string $ticketOrderId ) : stdClass
	{
		$query     = 'SELECT * FROM `ticketOrderAddresses` WHERE orderId = :orderId LIMIT 1';
		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId' => $ticketOrderId,
			]
		);

		$this->guardStatementSucceeded( $statement );

		$ticketOrder = $statement->fetchObject();

		if ( false === $ticketOrder )
		{
			throw new RuntimeException( 'Ticket order address not found: ' . $ticketOrderId );
		}

		return $ticketOrder;
	}

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return array
	 */
	public function getTicketOrderItems( string $ticketOrderId ) : array
	{
		$query     = 'SELECT * FROM `ticketOrderItems` WHERE `orderId` = :orderId';
		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId' => $ticketOrderId,
			]
		);

		$this->guardStatementSucceeded( $statement );

		return (array)$statement->fetchAll( PDO::FETCH_OBJ );
	}

	/**
	 * @throws Throwable
	 * @return int
	 */
	public function getNextInvoiceIdSequence() : int
	{
		$this->database->beginTransaction();

		try
		{
			$query     = 'SELECT `sequence` FROM `ticketOrderInvoiceSequence` WHERE 1 FOR UPDATE';
			$statement = $this->database->query( $query );

			$this->guardStatementSucceeded( $statement );

			$sequence = (int)($statement->fetchColumn() ?: 1);
			$sequence++;

			$query     = 'UPDATE `ticketOrderInvoiceSequence` SET `sequence` = :sequence LIMIT 1';
			$statement = $this->database->prepare( $query );
			$statement->execute(
				[
					'sequence' => $sequence,
				]
			);

			$this->guardStatementSucceeded( $statement );

			$this->database->commit();

			return $sequence;
		}
		catch ( Throwable $e )
		{
			$this->database->rollBack();

			throw $e;
		}
	}

	/**
	 * @param Invoice $invoice
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function addInvoice( Invoice $invoice ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$query = 'INSERT INTO `ticketOrderInvoices` (orderId, invoiceId, date, pdf) 
					  VALUES (:orderId, :invoiceId, :date, :pdf)';

			$statement = $this->database->prepare( $query );
			$statement->execute(
				[
					'orderId'   => $invoice->getOrderId()->toString(),
					'invoiceId' => $invoice->getId()->toString(),
					'date'      => $invoice->getDate()->format( 'c' ),
					'pdf'       => $invoice->getPdfFile()->getFileContent(),
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
	 * @param TicketOrderId     $ticketOrderId
	 * @param DateTimeImmutable $sentAt
	 *
	 * @throws PDOException
	 * @throws Throwable
	 */
	public function markEmailAsSent( TicketOrderId $ticketOrderId, DateTimeImmutable $sentAt ) : void
	{
		$this->database->beginTransaction();

		try
		{
			$query = 'INSERT INTO `ticketOrderMails` (orderId, sentAt) 
					  VALUES (:orderId, :sentAt)
					  ON DUPLICATE KEY UPDATE sentAt = :sentAt';

			$statement = $this->database->prepare( $query );
			$statement->execute(
				[
					'orderId' => $ticketOrderId->toString(),
					'sentAt'  => $sentAt->format( 'c' ),
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
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return null|stdClass
	 */
	public function getInvoiceRecordIfExists( string $ticketOrderId ) : ?stdClass
	{
		$query = 'SELECT `invoiceId`, `date`, `pdf` 
				  FROM `ticketOrderInvoices` 
				  WHERE `orderId` = :orderId 
				  LIMIT 1';

		$statement = $this->database->prepare( $query );
		$statement->execute(
			[
				'orderId' => $ticketOrderId,
			]
		);

		$this->guardStatementSucceeded( $statement );

		$invoice = $statement->fetchObject();

		return $invoice ?: null;
	}

	/**
	 * @param DateTimeImmutable $date
	 *
	 * @throws RuntimeException
	 * @return ProvidesSummaryArray
	 */
	public function getTicketSaleSummary( DateTimeImmutable $date ) : ProvidesSummaryArray
	{
		$queryOrdersDay = 'SELECT 
						COUNT(DISTINCT o.orderId) AS `purchasesDay`, 
						SUM(o.`paymentTotal` - o.`paymentFee`) AS `totalDay`, 
						SUM(o.diversityDonation) AS `diversityDonationDay`
					 FROM `ticketOrders` AS o
					 WHERE o.`date` BETWEEN :start AND :end';

		$queryItemsDay = 'SELECT
							COUNT(DISTINCT oi.itemId) AS `attendeesDay`
						  FROM ticketOrderItems AS oi
						  JOIN ticketOrders AS o USING (orderId)
						  WHERE o.`date` BETWEEN :start AND :end';

		$queryOrdersOverall = 'SELECT 
							COUNT(DISTINCT o.orderId) AS `purchasesOverall`, 
							SUM(o.`paymentTotal` - o.`paymentFee`) AS `totalOverall`,
							SUM(o.diversityDonation) AS `diversityDonationOverall`,
							GROUP_CONCAT(DISTINCT oa.countryCode SEPARATOR \', \') AS `attendeeCountries`
					     FROM `ticketOrders` AS o 
					 	 JOIN `ticketOrderAddresses` AS oa USING (orderId)
					     WHERE 1';

		$queryItemsOverall = 'SELECT
							COUNT(DISTINCT oi.itemId) AS `attendeesOverall`,
							SUM(IF(oi.ticketId IN (\'PHPDD18-EB-01\', \'PHPDD18-CT-01\'), 1, 0))  AS `attendeesConference`,
							SUM(IF(oi.ticketId IN (\'PHPDD18-EB-01\', \'PHPDD18-CT-01\'), 0, 1))  AS `attendeesWorkshops`
						  FROM ticketOrderItems AS oi
						  JOIN ticketOrders AS o USING (orderId)
						  WHERE 1';

		$statementOrdersDay = $this->database->prepare( $queryOrdersDay );
		$statementOrdersDay->execute(
			[
				'start' => $date->format( 'Y-m-d 00:00:00' ),
				'end'   => $date->format( 'Y-m-d 23:59:59' ),
			]
		);

		$this->guardStatementSucceeded( $statementOrdersDay );

		$data = (array)$statementOrdersDay->fetch( PDO::FETCH_ASSOC );

		$statementItemsDay = $this->database->prepare( $queryItemsDay );
		$statementItemsDay->execute(
			[
				'start' => $date->format( 'Y-m-d 00:00:00' ),
				'end'   => $date->format( 'Y-m-d 23:59:59' ),
			]
		);

		$this->guardStatementSucceeded( $statementItemsDay );

		$data = array_merge( $data, (array)$statementItemsDay->fetch( PDO::FETCH_ASSOC ) );

		$statementOrdersOverall = $this->database->query( $queryOrdersOverall );

		$this->guardStatementSucceeded( $statementOrdersOverall );

		$data = array_merge( $data, (array)$statementOrdersOverall->fetch( PDO::FETCH_ASSOC ) );

		$statementItemsOverall = $this->database->query( $queryItemsOverall );

		$this->guardStatementSucceeded( $statementItemsOverall );

		$data = array_merge( $data, (array)$statementItemsOverall->fetch( PDO::FETCH_ASSOC ) );

		return new TicketSaleSummary(
			$date,
			(int)($data['purchasesDay'] ?? '0'),
			(int)($data['purchasesOverall'] ?? '0'),
			(int)($data['totalDay'] ?? '0'),
			(int)($data['totalOverall'] ?? '0'),
			(int)($data['attendeesDay'] ?? '0'),
			(int)($data['attendeesOverall'] ?? '0'),
			(int)($data['attendeesWorkshops'] ?? '0'),
			(int)($data['attendeesConference'] ?? '0'),
			$data['attendeeCountries'] ?? '',
			(int)($data['diversityDonationDay'] ?? '0'),
			(int)($data['diversityDonationOverall'] ?? '0')
		);
	}
}