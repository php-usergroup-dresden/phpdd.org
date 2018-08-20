<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;

final class TicketsRefund
{
	/** @var TicketOrderId */
	private $ticketOrderId;

	/** @var array */
	private $ticketsToRefund;

	/** @var TicketOrderPayment */
	private $refundPayment;

	/** @var Money */
	private $refundMoney;

	/**
	 * @param TicketOrderId      $ticketOrderId
	 * @param array              $ticketsToRefund
	 * @param TicketOrderPayment $refundPayment
	 * @param Money              $refundMoney
	 */
	public function __construct(
		TicketOrderId $ticketOrderId,
		array $ticketsToRefund,
		TicketOrderPayment $refundPayment,
		Money $refundMoney
	)
	{
		$this->ticketOrderId   = $ticketOrderId;
		$this->ticketsToRefund = $ticketsToRefund;
		$this->refundPayment   = $refundPayment;
		$this->refundMoney     = $refundMoney;
	}

	public function getTicketOrderId() : TicketOrderId
	{
		return $this->ticketOrderId;
	}

	public function getTicketsToRefund() : array
	{
		return $this->ticketsToRefund;
	}

	public function getRefundPayment() : TicketOrderPayment
	{
		return $this->refundPayment;
	}

	public function getRefundMoney() : Money
	{
		return $this->refundMoney;
	}
}