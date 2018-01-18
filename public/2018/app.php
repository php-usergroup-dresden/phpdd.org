<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website;

use IceHawk\IceHawk\IceHawk;
use PHPUGDD\PHPDD\Website\Application\Configs\IceHawkConfig;
use PHPUGDD\PHPDD\Website\Infrastructure\IceHawkDelegate;

require __DIR__ . '/../../vendor/autoload.php';

$env      = new Env();
$config   = new IceHawkConfig( $env );
$delegate = new IceHawkDelegate( $env );
$icehawk  = new IceHawk( $config, $delegate );

$icehawk->init();
$icehawk->handleRequest();
