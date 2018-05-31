<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;

interface CalculatesPaymentFee
{
	public function getPaymentFee( Money $total, CountryCode $countryCode ) : Money;
}