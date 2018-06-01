<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;

final class TicketOrderPayment
{
	/** @var PaymentProvider */
	private $paymentProvider;

	/** @var PaymentId */
	private $paymentId;

	/** @var PayerId */
	private $payerId;

	/** @var DateTimeImmutable */
	private $executedAt;

	/** @var array */
	private $metaData;

	/**
	 * @param PaymentProvider   $paymentProvider
	 * @param PaymentId         $paymentId
	 * @param PayerId           $payerId
	 * @param array             $metaData
	 */
	public function __construct(
		PaymentProvider $paymentProvider,
		PaymentId $paymentId,
		PayerId $payerId,
		array $metaData = []
	)
	{
		$this->paymentProvider = $paymentProvider;
		$this->paymentId       = $paymentId;
		$this->payerId         = $payerId;
		$this->metaData        = $metaData;
	}

	public function getPaymentProvider() : PaymentProvider
	{
		return $this->paymentProvider;
	}

	public function getPaymentId() : PaymentId
	{
		return $this->paymentId;
	}

	public function getPayerId() : PayerId
	{
		return $this->payerId;
	}

	public function getMetaData() : array
	{
		return $this->metaData;
	}
}