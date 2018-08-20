<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use mikehaertl\wkhtmlto\Pdf;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItemStatus;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use stdClass;
use Swift_Attachment;
use Swift_Message;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function count;
use function dirname;

final class ChangeAttendeeNameCommand extends AbstractConsoleCommand
{
	use MoneyProviding;

	private const EMAIL_SUBJECT        = 'Ticket update for PHP Developer Days 2018';

	private const QR_CODE_BASE_URL     = 'https://2018.phpdd.org/tickets/scan';

	private const WKHTMLTOPDF_LOCATION = '/usr/local/bin/wkhtmltopdf';

	/** @var TicketOrderRepository */
	private $repository;

	protected function configure() : void
	{
		$this->setDescription( 'Changes the names of attendees in tickets.' );
		$this->addArgument( 'orderId', InputArgument::REQUIRED, 'Ticket-Order-ID' );
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int
	{
		$this->initStyle( $input, $output );
		$style             = $this->getStyle();
		$ticketOrderId     = $input->getArgument( 'orderId' );
		$database          = $this->getEnv()->getDatabase();
		$this->repository  = new TicketOrderRepository( $database );
		$ticketsConfig     = TicketsConfig::fromConfigFile();
		$emailConfig       = $this->getEnv()->getEmailConfig();
		$templateRenderer  = $this->getEnv()->getTemplateRenderer();
		$imageDir          = dirname( __DIR__, 5 ) . '/public/2018/assets/images';
		$ticketCssFilePath = dirname( __DIR__, 2 ) . '/Tickets/Templates/Ticket.css';
		$ticketPdfOptions  = [
			'encoding'         => 'UTF-8',
			'no-outline',
			'margin-top'       => 12,
			'margin-right'     => 12,
			'margin-bottom'    => 12,
			'margin-left'      => 20,
			'disable-smart-shrinking',
			'user-style-sheet' => $ticketCssFilePath,
		];

		try
		{
			$ticketOrder    = $this->getTicketOrderRecord( $ticketOrderId );
			$billingAddress = $this->getTicketOrderAddressRecord( $ticketOrderId );
			$this->printOrderSection( $ticketOrder );
			$ticketItems      = $this->getTicketOrderItems( $ticketOrderId, $ticketsConfig );
			$itemsToChange    = $this->askForTicketItemsToChange( $ticketItems );
			$countTicketItems = count( $itemsToChange );
			$updateTickets    = [];

			foreach ( $itemsToChange as $item )
			{
				$item->attendeeName             = $this->askForNewAttendeeName( $item );
				$updateTickets[ $item->itemId ] = $item->attendeeName;
			}

			$style->text( 'Your changed tickets:' . $this->getTicketListText( $itemsToChange ) );

			$style->confirm( 'Produce new tickets now?' );

			$style->progressStart( $countTicketItems );

			$ticketPdfs = [];

			foreach ( $itemsToChange as $ticketItem )
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

			$style->writeln( 'Ticket PDFs saved:' );
			$style->listing( $ticketPdfs );

			$style->writeln( 'Updating attendee names...' );

			$this->repository->updateTicketAttendeeNames( $updateTickets );

			$style->writeln( 'Composing email...' );

			$this->sendEmail( $ticketOrder, $billingAddress, $ticketPdfs );

			$style->success( 'Email sent to ' . $ticketOrder->email );

			return 0;
		}
		catch ( Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );

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
	private function askForTicketItemsToChange( array $ticketItems ) : array
	{
		$style               = $this->getStyle();
		$selectedTicketItems = [];

		$question = "For which tickets the attendee name should be changed?\n Type the numbers separated by commas or 'all' for all Tickets.";

		$answer = $style->ask( $question . $this->getTicketListText( $ticketItems ) );

		if ( 'all' === strtolower( (string)$answer ) )
		{
			$style->text( "Your selected tickets:\n" . $this->getTicketListText( $ticketItems ) );

			return $ticketItems;
		}

		if ( '' === trim( (string)$answer ) )
		{
			$style->error( 'Please select at least one ticket for refund.' );

			return $this->askForTicketItemsToChange( $ticketItems );
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

			return $this->askForTicketItemsToChange( $ticketItems );
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
			$ticketList .= "\n " . ($index + 1) . ') ' . $this->getTicketListItem( $item );
		}

		return $ticketList;
	}

	/**
	 * @param stdClass $item
	 *
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	private function getTicketListItem( stdClass $item ) : string
	{
		return sprintf(
			'%s - %s%s - €%s [%s]',
			$item->ticketId,
			$item->attendeeName,
			$item->discountCode ? " (Discount code: {$item->discountCode})" : '',
			$this->getDecimalFormattedMoney( (int)$item->ticketPrice ),
			strtoupper( (string)$item->status )
		);
	}

	/**
	 * @param stdClass $ticketItem
	 *
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	private function askForNewAttendeeName( stdClass $ticketItem ) : string
	{
		$style = $this->getStyle();

		$attendeeName = $style->ask( 'New attendee name for ' . $this->getTicketListItem( $ticketItem ) );

		if ( null === $attendeeName )
		{
			$style->error( 'Please enter an attendee name.' );

			return $this->askForNewAttendeeName( $ticketItem );
		}

		return $attendeeName;
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
	 * @param stdClass $ticketOrder
	 * @param stdClass $billingAddress
	 * @param array    $attachments
	 *
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
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
			'ticketCount'    => count( $attachments ),
		];
		$emailContent = $templateRenderer->renderWithData( 'TicketChangeEmail.twig', $emailData );

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