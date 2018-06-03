<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces;

interface ProvidesSummaryArray
{
	public function toArray() : array;
}