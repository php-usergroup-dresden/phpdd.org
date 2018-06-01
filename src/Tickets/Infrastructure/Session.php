<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure;

use IceHawk\Bridges\SessionForms\AbstractSession;
use IceHawk\Forms\Form;
use IceHawk\Forms\FormId;

final class Session extends AbstractSession
{
	private const TICKET_SELECTION_FORM_ID = 'ticketSelectionForm';

	private const TICKET_DETAILS_FORM_ID   = 'ticketDetailsForm';

	private const TICKET_PAYMENT_FORM_ID   = 'ticketPaymentForm';

	public function getTicketSelectionForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_SELECTION_FORM_ID ) );
	}

	public function getTicketDetailsForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_DETAILS_FORM_ID ) );
	}

	public function getTicketPaymentForm() : Form
	{
		return $this->getForm( new FormId( self::TICKET_PAYMENT_FORM_ID ) );
	}

	public function resetTicketOrder() : void
	{
		$this->unsetForm( new FormId( self::TICKET_SELECTION_FORM_ID ) );
		$this->unsetForm( new FormId( self::TICKET_DETAILS_FORM_ID ) );
		$this->unsetForm( new FormId( self::TICKET_PAYMENT_FORM_ID ) );
	}
}
