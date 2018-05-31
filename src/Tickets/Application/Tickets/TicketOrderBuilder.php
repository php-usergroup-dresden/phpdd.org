<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\DiscountConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculatorFactory;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\City;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\VatNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\ZipCode;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class TicketOrderBuilder
{
	use MoneyProviding;

	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var DiscountsConfig */
	private $discountsConfig;

	public function __construct( TicketsConfig $ticketsConfig, DiscountsConfig $discountsConfig )
	{
		$this->ticketsConfig   = $ticketsConfig;
		$this->discountsConfig = $discountsConfig;
	}

	/**
	 * @param string $ticketOrderId
	 * @param array  $selectedTickets
	 * @param array  $ticketDetails
	 *
	 * @throws Exceptions\AllowedTicketCountExceededException
	 * @throws Exceptions\AllowedTicketCountPerAttendeeExceededException
	 * @throws Exceptions\DiscountExceededTicketPriceException
	 * @throws Exceptions\DiscountNotAllowedForTicketException
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws DiscountConfigNotFoundException
	 * @throws RuntimeException
	 * @throws \Exception
	 * @return TicketOrder
	 */
	public function buildFromInputData(
		string $ticketOrderId,
		array $selectedTickets,
		array $ticketDetails
	) : TicketOrder
	{
		$selectedTicketInfos = new SelectedTicketInfos( $this->ticketsConfig, $selectedTickets );

		$billingAddress = new TicketOrderBillingAddress(
			new Firstname( (string)$ticketDetails['firstname'] ),
			new Lastname( (string)$ticketDetails['lastname'] ),
			$ticketDetails['companyName'] ? new CompanyName( (string)$ticketDetails['companyName'] ) : null,
			new StreetWithNumber( $ticketDetails['streetWithNumber'] ),
			$ticketDetails['addressAddon'] ? new AddressAddon( (string)$ticketDetails['addressAddon'] ) : null,
			new ZipCode( (string)$ticketDetails['zipCode'] ),
			new City( (string)$ticketDetails['city'] ),
			new CountryCode( (string)$ticketDetails['countryCode'] ),
			$ticketDetails['vatNumber'] ? new VatNumber( (string)$ticketDetails['vatNumber'] ) : null
		);

		$paymentProvider      = new PaymentProvider( (string)$ticketDetails['paymentProvider'] );
		$paymentFeeCalculator = (new PaymentFeeCalculatorFactory())->getCalculator( $paymentProvider );

		/** @var TicketOrderId $ticketOrderId */
		$ticketOrder = new TicketOrder(
			new TicketOrderId( $ticketOrderId ),
			new TicketOrderDate(),
			$paymentFeeCalculator
		);
		$ticketOrder->billTo( $billingAddress );

		$emailAddress = new TicketOrderEmailAddress( (string)$ticketDetails['email'] );
		$ticketOrder->sendTicketsAndInvoiceTo( $emailAddress );

		/** @var SelectedTicketInfo $ticketInfo */
		foreach ( $selectedTicketInfos->getTickets() as $ticketInfo )
		{
			$ticket    = $ticketInfo->getTicket();
			$attendees = (array)$ticketDetails['attendees'][ $ticket->getId()->toString() ];
			$discounts = (array)$ticketDetails['discounts'][ $ticket->getId()->toString() ];

			for ( $i = 0; $i < $ticketInfo->getQuantity(); $i++ )
			{
				$attendeeName = new AttendeeName( (string)$attendees[ $i ] );
				$ticketItem   = new TicketItem( $ticket, $attendeeName );

				if ( '' !== trim( $discounts[ $i ] ) )
				{
					$discountConfig = $this->discountsConfig->getDiscountConfigByTicketIdAndCode(
						$ticket->getId()->toString(),
						(string)$discounts[ $i ]
					);

					$discountItem = new DiscountItem(
						$discountConfig->getName(),
						new DiscountCode( (string)$discounts[ $i ] ),
						$discountConfig->getDescription(),
						$discountConfig->getDiscount(),
						$discountConfig->getAllowedTickets()
					);

					$ticketItem->grantDiscount( $discountItem );
				}

				$ticketOrder->orderTickets( $ticketItem );
			}
		}

		$diversityDonation = new DiversityDonation(
			$this->getMoney( (int)$ticketDetails['diversityDonation'] * 100 )
		);
		$ticketOrder->donateToDiversity( $diversityDonation );

		$ticketOrder->payWith( $paymentProvider );

		return $ticketOrder;
	}
}