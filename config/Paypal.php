<?php declare(strict_types=1);

return [
	'authEndpoint' => 'sandbox',
	'clientId'     => 'AbInKqyHMp9KeyMVSGs0y7U3Tn6hae7YS-xeidHiyYJBpv8SLEYeMvjsvkBTlUuX4lXavTr0tUOznYIP',
	'clientSecret' => 'ECFmrw-G68mTRrBmC8kKXDle-kgYxzF6OdRi64KoBoNcVZ1D89MrSIbjrN7GrE2GcoNHDpfOPZJCQeug',
	'redirectUrl'  => 'http://127.0.0.1:8080/tickets/',
	'scopes'       => 'profile email address phone https://uri.paypal.com/services/paypalattributes',
	'cancelUrl'    => 'http://127.0.0.1:8080/tickets/paypal-canceled',
	'successUrl'   => 'http://127.0.0.1:8080/tickets/paypal-success',
];