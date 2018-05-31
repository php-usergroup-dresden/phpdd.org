<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

abstract class AbstractResult
{
	/** @var int */
	private $resultType;

	/** @var string */
	private $errorMessage;

	public function __construct( int $resultType, string $errorMessage = '' )
	{
		$this->resultType   = $resultType;
		$this->errorMessage = $errorMessage;
	}

	public function failed() : bool
	{
		return (ResultType::FAILED === $this->resultType);
	}

	public function succeeded() : bool
	{
		return (ResultType::SUCCEEDED === $this->resultType);
	}

	public function getErrorMessage() : string
	{
		return $this->errorMessage;
	}
}