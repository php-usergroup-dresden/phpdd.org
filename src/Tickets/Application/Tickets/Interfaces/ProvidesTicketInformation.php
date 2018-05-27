<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

interface ProvidesTicketInformation
{
	public function getId() : TicketId;

	public function getType() : TicketType;

	public function getName() : TicketName;

	public function getDescription() : TicketDescription;

	public function getImage() : string;

	public function getPrice() : TicketPrice;

	public function getAvailableSeats() : int;

	public function getMaxSeatsPerOrder() : int;

	public function getValidFrom() : DateTimeImmutable;

	public function getValidTo() : DateTimeImmutable;

	public function isAvailable() : bool;
}