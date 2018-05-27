<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure;

use IceHawk\Bridges\SessionForms\AbstractSession;
use IceHawk\Forms\Form;
use IceHawk\Forms\FormId;

final class Session extends AbstractSession
{
	private const TICKET_SELECTION_FORM_ID = 'ticketSelectionForm';

	private const TICKET_DETAILS_FORM_ID   = 'ticketDetailsForm';

	public function getTicketSelectionForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_SELECTION_FORM_ID ) );
	}

	public function getTicketDetailsForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_DETAILS_FORM_ID ) );
	}

	public function abortTicketOrder() : void
	{
		$this->unsetForm( new FormId( self::TICKET_SELECTION_FORM_ID ) );
	}
}
