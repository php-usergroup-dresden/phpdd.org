<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Infrastructure\Configs;

use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\EmailConfig;
use PHPUnit\Framework\TestCase;

final class EmailConfigTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetValues() : void
	{
		$configData = [
			'smtpHost'      => 'smtp.server',
			'smtpPort'      => 587,
			'smtpUser'      => 'user',
			'smtpPassword'  => 'password',
			'useTls'        => true,
			'attachmentDir' => __DIR__,
			'sender'        => ['test@unit.de' => 'Unit Test'],
			'ccRecipients'  => ['cc@unit.de' => 'CC Unit Test'],
			'bccRecipients' => ['bcc@unit.de' => 'BCC Unit Test'],
			'replyTo'       => ['reply@unit.de' => 'Reply Unit Test'],
		];

		$emailConfig = new EmailConfig( $configData );

		$this->assertSame( 'smtp.server', $emailConfig->getSmtpHost() );
		$this->assertSame( 587, $emailConfig->getSmtpPort() );
		$this->assertSame( 'user', $emailConfig->getSmtpUser() );
		$this->assertSame( 'password', $emailConfig->getSmtpPassword() );
		$this->assertTrue( $emailConfig->useTls() );
		$this->assertSame( __DIR__, $emailConfig->getAttachmentDir() );
		$this->assertSame( ['test@unit.de' => 'Unit Test'], $emailConfig->getSender() );
		$this->assertSame( ['cc@unit.de' => 'CC Unit Test'], $emailConfig->getCcRecipients() );
		$this->assertSame( ['bcc@unit.de' => 'BCC Unit Test'], $emailConfig->getBccRecipients() );
		$this->assertSame( ['reply@unit.de' => 'Reply Unit Test'], $emailConfig->getReplyTo() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInstanceFromConfigFile() : void
	{
		/** @noinspection UnnecessaryAssertionInspection */
		$this->assertInstanceOf( EmailConfig::class, EmailConfig::fromConfigFile() );
	}
}
