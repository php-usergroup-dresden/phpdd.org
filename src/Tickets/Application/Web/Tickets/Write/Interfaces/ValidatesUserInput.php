<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces;

interface ValidatesUserInput
{
	public function failed() : bool;

	public function passed() : bool;

	public function getMessages() : array;
}