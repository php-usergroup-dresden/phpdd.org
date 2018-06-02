<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use PHPUGDD\PHPDD\Website\Tickets\Interfaces\ProvidesInfrastructure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractConsoleCommand extends Command
{
	/** @var ProvidesInfrastructure */
	private $env;

	/** @var SymfonyStyle */
	private $style;

	public function __construct( string $name = null, ProvidesInfrastructure $env )
	{
		parent::__construct( $name );
		$this->env = $env;
	}

	final protected function initStyle( InputInterface $input, OutputInterface $output ) : void
	{
		$this->style = new SymfonyStyle( $input, $output );
	}

	final protected function getStyle() : SymfonyStyle
	{
		return $this->style;
	}

	final protected function getEnv() : ProvidesInfrastructure
	{
		return $this->env;
	}
}