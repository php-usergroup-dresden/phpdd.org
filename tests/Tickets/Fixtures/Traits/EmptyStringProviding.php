<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits;

trait EmptyStringProviding
{
	public function emptyStringProvider() : array
	{
		return [
			[
				'srtring' => '',
			],
			[
				'srtring' => ' ',
			],
			[
				'srtring' => "\n",
			],
			[
				'srtring' => "\t",
			],
			[
				'srtring' => "\r",
			],
			[
				'srtring' => "\0",
			],
		];
	}
}