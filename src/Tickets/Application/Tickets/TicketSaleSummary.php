<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces\ProvidesSummaryArray;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class TicketSaleSummary implements ProvidesSummaryArray
{
	use MoneyProviding;

	/** @var DateTimeImmutable */
	private $date;

	/** @var int */
	private $purchasesDay;

	/** @var int */
	private $purchasesOverall;

	/** @var int */
	private $totalDay;

	/** @var int */
	private $totalOverall;

	public function __construct(
		DateTimeImmutable $date,
		int $purchasesDay,
		int $purchasesOverall,
		int $totalDay,
		int $totalOverall
	)
	{
		$this->date             = $date;
		$this->purchasesDay     = $purchasesDay;
		$this->purchasesOverall = $purchasesOverall;
		$this->totalDay         = $totalDay;
		$this->totalOverall     = $totalOverall;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function toArray() : array
	{
		$message = sprintf( 'Ticket sale summary for %s', $this->date->format( 'Y-m-d' ) );

		return [
			'fallback' => $message,
			'text'     => $message,
			'color'    => ($this->purchasesDay > 0) ? 'good' : 'danger',
			'fields'   => [
				[
					'title' => 'Purchases (day)',
					'value' => $this->purchasesDay,
					'short' => true,
				],
				[
					'title' => 'Total (day)',
					'value' => $this->getFormattedMoney( $this->totalDay ),
					'short' => true,
				],
				[
					'title' => 'Purchases (overall)',
					'value' => $this->purchasesOverall,
					'short' => true,
				],
				[
					'title' => 'Total (overall)',
					'value' => $this->getFormattedMoney( $this->totalOverall ),
					'short' => true,
				],
			],
		];
	}
}