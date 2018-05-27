<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;
use function array_merge_recursive;

final class CompositeValidator implements ValidatesUserInput
{
	/** @var array|ValidatesUserInput[] */
	private $validators = [];

	public function add( ValidatesUserInput $validator ) : void
	{
		$this->validators[] = $validator;
	}

	public function failed() : bool
	{
		$failed = false;
		foreach ( $this->validators as $validator )
		{
			if ( $validator->failed() )
			{
				$failed = true;
			}
		}

		return $failed;
	}

	public function passed() : bool
	{
		return !$this->failed();
	}

	public function getMessages() : array
	{
		$messages = [];

		foreach ( $this->validators as $validator )
		{
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$messages = array_merge_recursive( $messages, $validator->getMessages() );
		}

		return $messages;
	}
}