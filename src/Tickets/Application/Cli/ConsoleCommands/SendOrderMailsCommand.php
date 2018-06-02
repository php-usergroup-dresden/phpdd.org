<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use DateTimeImmutable;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use mikehaertl\wkhtmlto\Pdf;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Invoices\Invoice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoicePdfFile;
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
use function array_merge;
use function count;
use function dirname;
use function file_put_contents;
use function unlink;

final class SendOrderMailsCommand extends AbstractConsoleCommand
{
	use MoneyProviding;

	private const EMAIL_SUBJECT        = 'Ticket purchase for PHP Developer Days 2018';

	private const INVOICE_ID_PREFIX    = 'PHPDD18-';

	private const QR_CODE_BASE_URL     = 'https://2018.phpdd.org/tickets/scan';

	private const WKHTMLTOPDF_LOCATION = '/usr/local/bin/wkhtmltopdf';

	/** @var TicketOrderRepository */
	private $repository;

	protected function configure() : void
	{
		$this->setDescription( 'Sends an email to the buyer with invoice and tickets.' );
		$this->addArgument(
			'ticketOrderId',
			InputArgument::OPTIONAL,
			'Use a single ticketOrderId (optional)'
		);
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @throws RuntimeException
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @throws Throwable
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int
	{
		$this->initStyle( $input, $output );
		$this->repository = new TicketOrderRepository( $this->getEnv()->getDatabase() );

		$templateRenderer   = $this->getEnv()->getTemplateRenderer();
		$emailConfig        = $this->getEnv()->getEmailConfig();
		$style              = $this->getStyle();
		$ticketsConfig      = TicketsConfig::fromConfigFile();
		$ticketOrderIds     = $this->getTicketOrderIds( $input );
		$imageDir           = dirname( __DIR__, 5 ) . '/public/2018/assets/images';
		$invoiceCssFilePath = dirname( __DIR__, 2 ) . '/Invoices/Templates/TicketInvoice.css';
		$ticketCssFilePath  = dirname( __DIR__, 2 ) . '/Tickets/Templates/Ticket.css';

		$invoicePdfOptions = [
			'encoding'         => 'UTF-8',
			'no-outline',
			'margin-top'       => 12,
			'margin-right'     => 12,
			'margin-bottom'    => 12,
			'margin-left'      => 20,
			'disable-smart-shrinking',
			'user-style-sheet' => $invoiceCssFilePath,

		];

		$ticketPdfOptions                     = $invoicePdfOptions;
		$ticketPdfOptions['user-style-sheet'] = $ticketCssFilePath;

		$style->section( 'Using ticket order IDs:' );
		$style->listing( $ticketOrderIds );

		foreach ( $ticketOrderIds as $ticketOrderId )
		{
			$ticketOrder    = $this->getTicketOrderRecord( $ticketOrderId );
			$billingAddress = $this->getTicketOrderAddressRecord( $ticketOrderId );
			$ticketItems    = $this->getTicketOrderItems( $ticketOrderId, $ticketsConfig );

			$invoice = $this->getInvoiceIfExists( $ticketOrderId );

			if ( null === $invoice )
			{
				$invoiceExists   = false;
				$invoiceId       = $this->getNextInvoiceId( new DateTimeImmutable( $ticketOrder->date ) );
				$invoiceFilePath = sprintf( '%s/Invoice-%s.pdf', $emailConfig->getAttachmentDir(), $invoiceId );
				$invoiceDate     = new InvoiceDate();
			}
			else
			{
				$invoiceExists   = true;
				$invoiceId       = $invoice->getId()->toString();
				$invoiceFilePath = $invoice->getPdfFile()->toString();
				$invoiceDate     = $invoice->getDate();
			}

			$this->printOrderSection( $ticketOrder );
			$this->printAddress( $billingAddress );
			$this->printTicketOrderItems( $ticketItems );

			if ( $invoiceExists )
			{
				$style->writeln( 'Skipping invoice creation. Already exists: ' . $invoiceId );
			}
			else
			{
				$style->writeln( 'Creating invoice with ID: ' . $invoiceId );

				$invoiceData = [
					'imageDir'       => $imageDir,
					'invoiceId'      => $invoiceId,
					'ticketOrder'    => $ticketOrder,
					'billingAddress' => $billingAddress,
					'ticketItems'    => $ticketItems,
					'invoiceDate'    => $invoiceDate->format( 'm/d/Y' ),
				];

				$invoiceContent = $templateRenderer->renderWithData( 'TicketInvoice.twig', $invoiceData );

				$invoicePdf         = new Pdf( $invoicePdfOptions );
				$invoicePdf->binary = self::WKHTMLTOPDF_LOCATION;
				$invoicePdf->addPage( $invoiceContent );
				$invoicePdf->saveAs( $invoiceFilePath );

				$this->saveInvoice( $ticketOrderId, $invoiceId, $invoiceDate, $invoiceFilePath );

				$style->writeln( 'Invoice PDF saved: ' . $invoiceFilePath );
			}

			$countTicketItems = count( $ticketItems );
			$style->writeln( sprintf( 'Creating %d ticket(s)...', $countTicketItems ) );

			$style->progressStart( $countTicketItems );

			$ticketPdfs = [];

			foreach ( $ticketItems as $ticketItem )
			{
				$qrCodeFilePath = $this->createQRCodeForTicketItem(
					$ticketItem,
					$imageDir,
					$emailConfig->getAttachmentDir()
				);

				$data = [
					'imageDir'       => $imageDir,
					'ticketItem'     => $ticketItem,
					'qrCodeFilePath' => $qrCodeFilePath,
				];

				$ticketContent = $templateRenderer->renderWithData( 'Ticket.twig', $data );

				$ticketPdf         = new Pdf( $ticketPdfOptions );
				$ticketPdf->binary = self::WKHTMLTOPDF_LOCATION;
				$ticketPdf->addPage( $ticketContent );

				$ticketFilePath = sprintf( '%s/Tickets-%s.pdf', $emailConfig->getAttachmentDir(), $ticketItem->itemId );
				$ticketPdf->saveAs( $ticketFilePath );

				$ticketPdfs[] = $ticketFilePath;

				@unlink( $qrCodeFilePath );

				/** @noinspection DisconnectedForeachInstructionInspection */
				$style->progressAdvance();
			}

			$style->progressFinish();

			$style->writeln( 'Tickets PDF saved:' );
			$style->listing( $ticketPdfs );

			$style->writeln( 'Composing email...' );

			$attachments = array_merge( [$invoiceFilePath], $ticketPdfs );
			$this->sendEmail( $ticketOrder, $billingAddress, $attachments );

			$this->markEmailAsSent( $ticketOrderId );

			$style->success( 'Email sent to ' . $ticketOrder->email );
		}

		return 0;
	}

	/**
	 * @param InputInterface $input
	 *
	 * @throws RuntimeException
	 * @return array
	 */
	private function getTicketOrderIds( InputInterface $input ) : array
	{
		$ticketOrderId = $input->getArgument( 'ticketOrderId' );
		if ( null !== $ticketOrderId )
		{
			return [$ticketOrderId];
		}

		return $this->repository->getTicketOrderIdsNotHavingEmailsSent();
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
	 * @param string $ticketOrderId
	 *
	 * @throws RuntimeException
	 * @throws \Exception
	 * @return null|Invoice
	 */
	private function getInvoiceIfExists( string $ticketOrderId ) : ?Invoice
	{
		$invoiceRecord = $this->repository->getInvoiceRecordIfExists( $ticketOrderId );

		if ( null === $invoiceRecord )
		{
			return null;
		}

		$emailConfig     = $this->getEnv()->getEmailConfig();
		$invoiceFilePath = sprintf( '%s/Invoice-%s.pdf', $emailConfig->getAttachmentDir(), $invoiceRecord->invoiceId );
		file_put_contents( $invoiceFilePath, $invoiceRecord->pdf );

		$invoice = new Invoice(
			new InvoiceId( (string)$invoiceRecord->invoiceId ),
			new TicketOrderId( $ticketOrderId ),
			new InvoiceDate( (string)$invoiceRecord->date ),
			new InvoicePdfFile( $invoiceFilePath )
		);

		return $invoice;
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
	 * @param stdClass $order
	 *
	 * @throws \InvalidArgumentException
	 */
	private function printOrderSection( stdClass $order ) : void
	{
		$this->getStyle()->section(
			sprintf(
				'Processing ticket order %s (%s) - €%s',
				$order->orderId,
				$order->email,
				$this->getDecimalFormattedMoney( (int)$order->paymentTotal )
			)
		);
	}

	private function printAddress( stdClass $address ) : void
	{
		$this->getStyle()->writeln(
			sprintf(
				'%s%s %s • %s%s • %s-%s %s • VAT number: %s',
				$address->companyName ? "{$address->companyName} • " : '',
				$address->firstname,
				$address->lastname,
				$address->streetWithNumber,
				$address->addressAddon ? " • {$address->addressAddon}" : '',
				$address->countryCode,
				$address->zipCode,
				$address->city,
				$address->vatNumber ?: 'N/A'
			)
		);
	}

	private function printTicketOrderItems( array $items ) : void
	{
		$elements = [];
		foreach ( $items as $item )
		{
			$elements[] = sprintf(
				'%s - %s%s',
				$item->ticketId,
				$item->attendeeName,
				$item->discountCode ? " (Discount code: {$item->discountCode})" : ''
			);
		}

		$this->getStyle()->listing( $elements );
	}

	/**
	 * @param stdClass $ticketItem
	 * @param string   $imageDir
	 * @param string   $outputDir
	 *
	 * @throws \Endroid\QrCode\Exception\InvalidPathException
	 * @return string
	 */
	private function createQRCodeForTicketItem( stdClass $ticketItem, string $imageDir, string $outputDir ) : string
	{
		$url            = sprintf( '%s?ticketItemId=%s', self::QR_CODE_BASE_URL, $ticketItem->itemId );
		$outputFilePath = sprintf( '%s/QRCode-%s.png', $outputDir, $ticketItem->itemId );

		$qrCode = new QrCode( $url );
		$qrCode->setSize( 300 );
		$qrCode->setWriterByName( 'png' );
		$qrCode->setMargin( 10 );
		$qrCode->setEncoding( 'UTF-8' );
		$qrCode->setErrorCorrectionLevel( ErrorCorrectionLevel::HIGH );
		$qrCode->setForegroundColor( ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0] );
		$qrCode->setBackgroundColor( ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0] );
		$qrCode->setLogoPath( $imageDir . '/phpugdd-logo.png' );
		$qrCode->setLogoWidth( 100 );
		$qrCode->setRoundBlockSize( true );
		$qrCode->setValidateResult( true );

		$qrCode->writeFile( $outputFilePath );

		return $outputFilePath;
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
			$invoiceDate,
			new InvoicePdfFile( $invoicePdf )
		);

		$this->repository->addInvoice( $invoice );
	}

	private function sendEmail(
		stdClass $ticketOrder,
		stdClass $billingAddress,
		array $attachments
	) : void
	{
		$mailer           = $this->getEnv()->getMailer();
		$templateRenderer = $this->getEnv()->getTemplateRenderer();
		$emailConfig      = $this->getEnv()->getEmailConfig();

		$emailData    = [
			'ticketOrder'    => $ticketOrder,
			'billingAddress' => $billingAddress,
		];
		$emailContent = $templateRenderer->renderWithData( 'PurchaseEmail.twig', $emailData );

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

	/**
	 * @param string $ticketOrderId
	 *
	 * @throws Throwable
	 * @throws \PDOException
	 */
	private function markEmailAsSent( string $ticketOrderId ) : void
	{
		$this->repository->markEmailAsSent( new TicketOrderId( $ticketOrderId ), new DateTimeImmutable() );
	}
}