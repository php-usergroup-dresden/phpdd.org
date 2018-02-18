<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets;

use IceHawk\IceHawk\IceHawk;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\IceHawkConfig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\IceHawkDelegate;

require __DIR__ . '/../../vendor/autoload.php';

$env      = new Env();
$config   = new IceHawkConfig( $env );
$delegate = new IceHawkDelegate( $env );
$icehawk  = new IceHawk( $config, $delegate );

/** @noinspection PhpUnhandledExceptionInspection */
$icehawk->init();
$icehawk->handleRequest();
