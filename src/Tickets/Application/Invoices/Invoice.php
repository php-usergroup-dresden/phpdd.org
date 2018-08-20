<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Invoices;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoicePdfFile;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\InvoiceType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;

final class Invoice
{
	/** @var InvoiceId */
	private $id;

	/** @var TicketOrderId */
	private $orderId;

	/** @var InvoiceType */
	private $type;

	/** @var InvoiceDate */
	private $date;

	/** @var InvoicePdfFile */
	private $pdfFile;

	public function __construct(
		InvoiceId $id,
		TicketOrderId $orderId,
		InvoiceType $type,
		InvoiceDate $date,
		InvoicePdfFile $pdfFile
	)
	{
		$this->id      = $id;
		$this->orderId = $orderId;
		$this->type    = $type;
		$this->date    = $date;
		$this->pdfFile = $pdfFile;
	}

	public function getId() : InvoiceId
	{
		return $this->id;
	}

	public function getOrderId() : TicketOrderId
	{
		return $this->orderId;
	}

	public function getType() : InvoiceType
	{
		return $this->type;
	}

	public function getDate() : InvoiceDate
	{
		return $this->date;
	}

	public function getPdfFile() : InvoicePdfFile
	{
		return $this->pdfFile;
	}
}