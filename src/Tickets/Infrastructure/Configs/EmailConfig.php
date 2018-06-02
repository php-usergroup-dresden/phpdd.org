<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs;

final class EmailConfig
{
	/** @var array */
	private $configData;

	public function __construct( array $configData )
	{
		$this->configData = $configData;
	}

	public static function fromConfigFile() : self
	{
		$configData = (array)require __DIR__ . '/../../../../config/Email.php';

		return new self( $configData );
	}

	public function getSmtpHost() : string
	{
		return (string)$this->configData['smtpHost'];
	}

	public function getSmtpPort() : int
	{
		return (int)$this->configData['smtpPort'];
	}

	public function getSmtpUser() : string
	{
		return (string)$this->configData['smtpUser'];
	}

	public function getSmtpPassword() : string
	{
		return (string)$this->configData['smtpPassword'];
	}

	public function useTls() : bool
	{
		return (bool)$this->configData['useTls'];
	}

	public function getAttachmentDir() : string
	{
		return (string)$this->configData['attachmentDir'];
	}

	public function getSender() : array
	{
		return (array)$this->configData['sender'];
	}

	public function getCcRecipients() : array
	{
		return (array)$this->configData['ccRecipients'];
	}

	public function getBccRecipients() : array
	{
		return (array)$this->configData['bccRecipients'];
	}

	public function getReplyTo() : array
	{
		return (array)$this->configData['replyTo'];
	}
}