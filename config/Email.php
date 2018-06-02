<?php declare(strict_types=1);

return [
	'smtpHost'      => 'mail.agenturserver.de',
	'smtpPort'      => 587,
	'smtpUser'      => 'p268406p6',
	'smtpPassword'  => '',
	'useTls'        => true,
	'attachmentDir' => __DIR__ . '/../data/mail/attachments',
	'sender'        => ['orga@phpug-dresden.org' => 'PHP USERGROUP DRESDEN e.V.'],
	'ccRecipients'  => [],
	'bccRecipients' => ['holger.woltersdorf@phpug-dresden.org' => 'PHP USERGROUP DRESDEN e.V.'],
	'replyTo'       => ['orga@phpug-dresden.org' => 'PHP USERGROUP DRESDEN e.V.'],
];