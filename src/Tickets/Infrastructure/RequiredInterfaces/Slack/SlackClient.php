<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack;

use Maknz\Slack\Attachment;
use Maknz\Slack\Client;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces\ConfiguresSlackClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Slack\Interfaces\ProvidesSummaryArray;

final class SlackClient
{
	/** @var ConfiguresSlackClient */
	private $config;

	/** @var Client */
	private $client;

	public function __construct( ConfiguresSlackClient $config )
	{
		$this->config = $config;
	}

	private function getClient() : Client
	{
		if ( null === $this->client )
		{
			$this->client = new Client(
				$this->config->getWebHookUrl(),
				[
					'channel'  => 'phpdd18-tickets',
					'username' => 'TicketSale',
					'icon'     => ':ticket:',
				]
			);
		}

		return $this->client;
	}

	/**
	 * @param ProvidesSummaryArray $summaryArray
	 * @param null|string          $channel
	 *
	 * @throws \InvalidArgumentException
	 */
	public function sendSummary( ProvidesSummaryArray $summaryArray, ?string $channel = null ) : void
	{
		$message = $this->getClient()->createMessage();

		if ( null !== $channel )
		{
			$message->to( $channel );
		}

		$message->attach( new Attachment( $summaryArray->toArray() ) );
		$message->send();
	}
}