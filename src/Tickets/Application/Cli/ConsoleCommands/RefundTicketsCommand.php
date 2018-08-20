<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use DateTimeImmutable;
use mikehaertl\wkhtmlto\Pdf;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Invoices\Invoice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Invoices\InvoiceTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItemStatus;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderPayment;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketsRefund;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoicePdfFile;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use stdClass;
use Swift_Attachment;
use Swift_Message;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_map;
use function count;
use function dirname;
use const SORT_ASC;
use const SORT_NUMERIC;

final class RefundTicketsCommand extends AbstractConsoleCommand
{
	use MoneyProviding;

	private const EMAIL_SUBJECT        = 'Ticket refund for PHP Developer Days 2018';

	private const INVOICE_ID_PREFIX    = 'PHPDD18-';

	private const WKHTMLTOPDF_LOCATION = '/usr/local/bin/wkhtmltopdf';

	private const PAYER_ID             = 'orga@phpug-dresden.org';

	/** @var TicketOrderRepository */
	private $repository;

	protected function configure() : void
	{
		$this->setDescription( 'Allows refunding of tickets.' );
		$this->addArgument( 'orderId', InputArgument::REQUIRED, 'Ticket-Order-ID' );
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @throws \Throwable
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int
	{
		$this->initStyle( $input, $output );
		$style              = $this->getStyle();
		$ticketOrderId      = $input->getArgument( 'orderId' );
		$database           = $this->getEnv()->getDatabase();
		$this->repository   = new TicketOrderRepository( $database );
		$ticketsConfig      = TicketsConfig::fromConfigFile();
		$emailConfig        = $this->getEnv()->getEmailConfig();
		$templateRenderer   = $this->getEnv()->getTemplateRenderer();
		$imageDir           = dirname( __DIR__, 5 ) . '/public/2018/assets/images';
		$invoiceCssFilePath = dirname( __DIR__, 2 ) . '/Invoices/Templates/TicketInvoice.css';
		$invoicePdfOptions  = [
			'encoding'         => 'UTF-8',
			'no-outline',
			'margin-top'       => 12,
			'margin-right'     => 12,
			'margin-bottom'    => 12,
			'margin-left'      => 20,
			'disable-smart-shrinking',
			'user-style-sheet' => $invoiceCssFilePath,

		];

		try
		{
			$ticketOrder    = $this->getTicketOrderRecord( $ticketOrderId );
			$billingAddress = $this->getTicketOrderAddressRecord( $ticketOrderId );
			$this->printOrderSection( $ticketOrder );
			$ticketItems     = $this->getTicketOrderItems( $ticketOrderId, $ticketsConfig );
			$itemsToRefund   = $this->askForTicketItemsToRefund( $ticketItems );
			$refundMoney     = $this->askForRefundAmount( $itemsToRefund );
			$paymentProvider = $this->askForRefundPaymentProvider( $ticketOrder );

			$paymentId = PaymentId::newRefundId();
			$payerId   = new PayerId( self::PAYER_ID );
			$payment   = new TicketOrderPayment( $paymentProvider, $paymentId, $payerId, [] );

			$refund = new TicketsRefund(
				new TicketOrderId( $ticketOrderId ),
				$itemsToRefund,
				$payment,
				$refundMoney
			);

			$invoiceId       = $this->getNextInvoiceId( new DateTimeImmutable() );
			$invoiceFilePath = sprintf( '%s/Invoice-%s.pdf', $emailConfig->getAttachmentDir(), $invoiceId );
			$invoiceDate     = new InvoiceDate();

			$style->writeln( 'Creating refund invoice with ID: ' . $invoiceId );

			$invoiceData = [
				'imageDir'              => $imageDir,
				'invoiceId'             => $invoiceId,
				'ticketOrder'           => $ticketOrder,
				'billingAddress'        => $billingAddress,
				'ticketItems'           => $itemsToRefund,
				'invoiceDate'           => $invoiceDate->format( 'm/d/Y' ),
				'refundMoney'           => $refundMoney->negative(),
				'refundPaymentProvider' => $paymentProvider,
			];

			$invoiceContent = $templateRenderer->renderWithData( 'TicketRefundInvoice.twig', $invoiceData );

			$invoicePdf         = new Pdf( $invoicePdfOptions );
			$invoicePdf->binary = self::WKHTMLTOPDF_LOCATION;
			$invoicePdf->addPage( $invoiceContent );
			$invoicePdf->saveAs( $invoiceFilePath );

			$this->saveInvoice( $ticketOrderId, $invoiceId, $invoiceDate, $invoiceFilePath );

			$style->writeln( 'Refund invoice PDF saved: ' . $invoiceFilePath );

			$this->repository->refundTickets( $refund );

			$style->writeln( 'Composing refund email.' );

			$this->sendEmail( $ticketOrder, $billingAddress, [$invoiceFilePath], count( $itemsToRefund ) );

			$style->success( 'Refund executed and email sent.' );

			return 0;
		}
		catch ( Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );
			$style->error( $e->getMessage() );

			return 1;
		}
	}

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return stdClass
	 */
	private function getTicketOrderRecord( string $ticketOrderId ) : stdClass
	{
		return $this->repository->getTicketOrderRecord( $ticketOrderId );
	}

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @return stdClass
	 */
	private function getTicketOrderAddressRecord( string $ticketOrderId ) : stdClass
	{
		return $this->repository->getTicketOrderAddressRecord( $ticketOrderId );
	}

	/**
	 * @param stdClass $order
	 *
	 * @throws \InvalidArgumentException
	 */
	private function printOrderSection( stdClass $order ) : void
	{
		$style = $this->getStyle();
		$style->section(
			sprintf(
				'Processing ticket order %s (%s) - €%s',
				$order->orderId,
				$order->email,
				$this->getDecimalFormattedMoney( (int)$order->paymentTotal )
			)
		);

		$style->text(
			sprintf(
				'Already refunded: €%s',
				$this->getDecimalFormattedMoney( (int)$order->refundTotal )
			)
		);
	}

	/**
	 * @param string        $ticketOrderId
	 * @param TicketsConfig $ticketsConfig
	 *
	 * @throws RuntimeException
	 * @return array
	 */
	private function getTicketOrderItems( string $ticketOrderId, TicketsConfig $ticketsConfig ) : array
	{
		$recordItems = $this->repository->getTicketOrderItems( $ticketOrderId );

		return array_map(
			function ( stdClass $item ) use ( $ticketsConfig )
			{
				$ticketId                = new TicketId( (string)$item->ticketId );
				$ticketConfig            = $ticketsConfig->findTicketById( $ticketId );
				$item->ticketName        = $ticketConfig->getName()->toString();
				$item->ticketDescription = $ticketConfig->getDescription()->toString();
				$item->ticketPrice       = $ticketConfig->getPrice()->getMoney()->getAmount();

				return $item;
			},
			$recordItems
		);
	}

	/**
	 * @param array $ticketItems
	 *
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	private function askForTicketItemsToRefund( array $ticketItems ) : array
	{
		$style               = $this->getStyle();
		$selectedTicketItems = [];

		$question = "Which tickets should be refunded?\n Type the numbers separated by commas or 'all' for all Tickets.";

		$answer = $style->ask( $question . $this->getTicketListText( $ticketItems ) );

		if ( 'all' === strtolower( (string)$answer ) )
		{
			$style->text( "Your selected tickets:\n" . $this->getTicketListText( $ticketItems ) );

			return $ticketItems;
		}

		if ( '' === trim( (string)$answer ) )
		{
			$style->error( 'Please select at least one ticket for refund.' );

			return $this->askForTicketItemsToRefund( $ticketItems );
		}

		$numbers = array_filter( array_map( 'trim', explode( ',', $answer ) ) );
		sort( $numbers, SORT_NUMERIC | SORT_ASC );

		foreach ( $numbers as $number )
		{
			$index = (int)$number - 1;
			if ( !isset( $ticketItems[ $index ] ) )
			{
				continue;
			}

			if ( TicketItemStatus::REFUNDED === $ticketItems[ $index ]->status )
			{
				continue;
			}

			$selectedTicketItems[ $index ] = $ticketItems[ $index ];
		}

		if ( 0 === count( $selectedTicketItems ) )
		{
			$style->error( 'Please select at least one ticket for refund.' );

			return $this->askForTicketItemsToRefund( $ticketItems );
		}

		$style->text( 'Your selected tickets:' . $this->getTicketListText( $selectedTicketItems ) );

		return $selectedTicketItems;
	}

	/**
	 * @param array $ticketItems
	 *
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	private function getTicketListText( array $ticketItems ) : string
	{
		$ticketList = '';
		foreach ( $ticketItems as $index => $item )
		{
			$ticketList .= sprintf(
				"\n %d) %s - %s%s - €%s [%s]",
				$index + 1,
				$item->ticketId,
				$item->attendeeName,
				$item->discountCode ? " (Discount code: {$item->discountCode})" : '',
				$this->getDecimalFormattedMoney( (int)$item->ticketPrice ),
				strtoupper( (string)$item->status )
			);
		}

		return $ticketList;
	}

	/**
	 * @param array $itemsToRefund
	 *
	 * @throws \InvalidArgumentException
	 * @return Money
	 */
	private function askForRefundAmount( array $itemsToRefund ) : Money
	{
		$style = $this->getStyle();

		$total = 0;
		foreach ( $itemsToRefund as $ticketItem )
		{
			$total += (int)$ticketItem->ticketPrice;
		}

		$refundAmount = $style->ask( 'Amount to refund (100 = 1€)', $total );

		if ( null === $refundAmount )
		{
			$style->error( 'Please provide a refund amount.' );

			return $this->askForRefundAmount( $itemsToRefund );
		}

		$style->text( sprintf( 'Refund will be €%s', $this->getDecimalFormattedMoney( (int)$refundAmount ) ) );

		return $this->getMoney( (int)$refundAmount );
	}

	private function askForRefundPaymentProvider( stdClass $ticketOrder ) : PaymentProvider
	{
		$style = $this->getStyle();

		$paymentProvider = $style->choice(
			'Which payment provider is used for refunding?',
			PaymentProviders::TRANSFERABLES,
			$ticketOrder->paymentProvider
		);

		if ( null === $paymentProvider )
		{
			$style->error( 'Please choose a payment provider for refunding.' );

			return $this->askForRefundPaymentProvider( $ticketOrder );
		}

		$style->text( sprintf( 'Refunding will be executed via %s.', $paymentProvider ) );

		return new PaymentProvider( $paymentProvider );
	}

	/**
	 * @param DateTimeImmutable $ticketOrderDate
	 *
	 * @throws Throwable
	 * @return string
	 */
	private function getNextInvoiceId( DateTimeImmutable $ticketOrderDate ) : string
	{
		$nextSequence = $this->repository->getNextInvoiceIdSequence();

		return sprintf(
			'%s%s-%04d',
			self::INVOICE_ID_PREFIX,
			$ticketOrderDate->format( 'Y-m-d' ),
			$nextSequence
		);
	}

	/**
	 * @param string      $ticketOrderId
	 * @param string      $invoiceId
	 * @param InvoiceDate $invoiceDate
	 * @param string      $invoicePdf
	 *
	 * @throws Throwable
	 * @throws \PDOException
	 */
	private function saveInvoice(
		string $ticketOrderId,
		string $invoiceId,
		InvoiceDate $invoiceDate,
		string $invoicePdf
	) : void
	{
		$invoice = new Invoice(
			new InvoiceId( $invoiceId ),
			new TicketOrderId( $ticketOrderId ),
			new InvoiceType( InvoiceTypes::REFUND ),
			$invoiceDate,
			new InvoicePdfFile( $invoicePdf )
		);

		$this->repository->addInvoice( $invoice );
	}

	/**
	 * @param stdClass $ticketOrder
	 * @param stdClass $billingAddress
	 * @param array    $attachments
	 * @param int      $ticketCount
	 *
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	private function sendEmail(
		stdClass $ticketOrder,
		stdClass $billingAddress,
		array $attachments,
		int $ticketCount
	) : void
	{
		$mailer           = $this->getEnv()->getMailer();
		$templateRenderer = $this->getEnv()->getTemplateRenderer();
		$emailConfig      = $this->getEnv()->getEmailConfig();

		$emailData    = [
			'ticketOrder'    => $ticketOrder,
			'billingAddress' => $billingAddress,
			'ticketCount'    => $ticketCount,
		];
		$emailContent = $templateRenderer->renderWithData( 'RefundEmail.twig', $emailData );

		$email = new Swift_Message( self::EMAIL_SUBJECT );
		$email->setBody( $emailContent, 'text/html', 'utf-8' );
		$email->setTo( [$ticketOrder->email => "{$billingAddress->firstname} {$billingAddress->lastname}"] );
		$email->setFrom( $emailConfig->getSender() );
		$email->setReplyTo( $emailConfig->getReplyTo() );
		$email->setBcc( $emailConfig->getBccRecipients() );
		$email->setCc( $emailConfig->getCcRecipients() );

		foreach ( $attachments as $attachment )
		{
			$email->attach( Swift_Attachment::fromPath( $attachment, 'application/pdf' ) );
		}

		$mailer->send( $email );
	}
}