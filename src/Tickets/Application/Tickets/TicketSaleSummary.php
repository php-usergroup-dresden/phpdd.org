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

	/** @var int */
	private $attendeesDay;

	/** @var int */
	private $attendeesOverall;

	/** @var int */
	private $attendeesWorkshops;

	/** @var int */
	private $attendeesConference;

	/** @var string */
	private $attendeeCountries;

	/** @var int */
	private $diversityDonationDay;

	/** @var int */
	private $diversityDonationOverall;

	public function __construct(
		DateTimeImmutable $date,
		int $purchasesDay,
		int $purchasesOverall,
		int $totalDay,
		int $totalOverall,
		int $attendeesDay,
		int $attendeesOverall,
		int $attendeesWorkshops,
		int $attendeesConference,
		string $attendeeCountries,
		int $diversityDonationDay,
		int $diversityDonationOverall
	)
	{
		$this->date                     = $date;
		$this->purchasesDay             = $purchasesDay;
		$this->purchasesOverall         = $purchasesOverall;
		$this->totalDay                 = $totalDay;
		$this->totalOverall             = $totalOverall;
		$this->attendeesDay             = $attendeesDay;
		$this->attendeesOverall         = $attendeesOverall;
		$this->attendeesWorkshops       = $attendeesWorkshops;
		$this->attendeesConference      = $attendeesConference;
		$this->attendeeCountries        = $attendeeCountries;
		$this->diversityDonationDay     = $diversityDonationDay;
		$this->diversityDonationOverall = $diversityDonationOverall;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function toArray() : array
	{
		$message = "Ticket sale summary:\n";

		return [
			'fallback' => $message,
			'text'     => $message,
			'color'    => ($this->purchasesDay > 0) ? 'good' : 'danger',
			'fields'   => [
				[
					'title' => sprintf( 'Purchases (%s)', $this->date->format( 'Y-m-d' ) ),
					'value' => sprintf( '%d (%d attendees)', $this->purchasesDay, $this->attendeesDay ),
					'short' => false,
				],
				[
					'title' => sprintf( 'Total (%s)', $this->date->format( 'Y-m-d' ) ),
					'value' => $this->getFormattedMoney( $this->totalDay ),
					'short' => false,
				],
				[
					'title' => sprintf( 'Diversity Donations (%s)', $this->date->format( 'Y-m-d' ) ),
					'value' => $this->getFormattedMoney( $this->diversityDonationDay ),
					'short' => false,
				],
				[
					'title' => 'Purchases (overall)',
					'value' => sprintf( '%d (%d attendees)', $this->purchasesOverall, $this->attendeesOverall ),
					'short' => false,
				],
				[
					'title' => 'Total (overall)',
					'value' => $this->getFormattedMoney( $this->totalOverall ),
					'short' => false,
				],
				[
					'title' => 'Diversity Donations (overall)',
					'value' => $this->getFormattedMoney( $this->diversityDonationOverall ),
					'short' => false,
				],
				[
					'title' => 'Participation',
					'value' => sprintf(
						'Workshops: %d / Conference: %d',
						$this->attendeesWorkshops,
						$this->attendeesConference
					),
					'short' => false,
				],
				[
					'title' => 'Purchases from countries',
					'value' => $this->attendeeCountries,
					'short' => false,
				],
			],
		];
	}
}