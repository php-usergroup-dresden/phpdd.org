<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Web;

use hollodotme\FluidValidator\CheckMode;
use hollodotme\FluidValidator\FluidValidator;
use hollodotme\FluidValidator\MessageCollectors\GroupedListMessageCollector;
use PHPUGDD\PHPDD\Website\Application\Bridges\UserInput;

/**
 * Class AbstractUserInputValidator
 * @package PHPUGDD\PHPDD\Website\Application\Web
 */
abstract class AbstractUserInputValidator
{
	/** @var FluidValidator */
	private $validator;

	public function __construct( UserInput $userInput )
	{
		$this->validator = new FluidValidator( CheckMode::CONTINUOUS, $userInput, new GroupedListMessageCollector() );
	}

	abstract protected function validate( FluidValidator $validator ) : void;

	final public function failed() : bool
	{
		$this->validator->reset();
		$this->validate( $this->validator );

		return $this->validator->failed();
	}

	final public function passed() : bool
	{
		$this->validator->reset();
		$this->validate( $this->validator );

		return $this->validator->passed();
	}

	final public function getMessages() : array
	{
		return $this->validator->getMessages();
	}
}
