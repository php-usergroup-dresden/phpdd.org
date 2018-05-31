<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

interface ProvidesActionResult
{
	public function failed() : bool;

	public function succeeded() : bool;

	public function getErrorMessage() : string;
}