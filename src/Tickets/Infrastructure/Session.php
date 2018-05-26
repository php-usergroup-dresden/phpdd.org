<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure;

use IceHawk\Bridges\SessionForms\AbstractSession;
use IceHawk\Forms\Form;
use IceHawk\Forms\FormId;

final class Session extends AbstractSession
{
	private const TICKET_SELECTION_FORM = 'ticketSelectionForm';

	public function getTicketSelectionForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_SELECTION_FORM ) );
	}

	public function resetTicketOrderProcess() : void
	{
		$this->unsetForm( new FormId( self::TICKET_SELECTION_FORM ) );
	}
}
