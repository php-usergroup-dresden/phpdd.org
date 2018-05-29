<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces;

interface ProvidesDiscountCodes
{
	public function getDiscountCodesForTicketId( string $ticketId ) : array;
}