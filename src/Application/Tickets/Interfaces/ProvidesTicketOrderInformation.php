<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Application\Tickets\DiscountItemCollection;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketItemCollection;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketOrderBillingAddress;
use PHPUGDD\PHPDD\Website\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderTotal;

/**
 * Interface ProvidesTicketOrderInformation
 * @package PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces
 */
interface ProvidesTicketOrderInformation
{
	public function getOrderId() : TicketOrderId;

	public function getOrderDate() : TicketOrderDate;

	public function getTicketItems() : TicketItemCollection;

	public function getDiversityDonation() : DiversityDonation;

	public function getEmailAddress() : TicketOrderEmailAddress;

	public function getBillingAddress() : TicketOrderBillingAddress;

	public function getDiscountItems() : DiscountItemCollection;

	public function getOrderTotal() : TicketOrderTotal;

	public function getDiscountTotal() : TicketOrderDiscountTotal;

	public function getPaymentTotal() : TicketOrderPaymentTotal;
}
