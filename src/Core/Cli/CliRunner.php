<?php namespace Atomino\Core\Cli;

use Atomino\Core\Runner\CliRunnerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CliRunner implements CliRunnerInterface {

	private Application $application;

	public function __construct() {
		$this->application = new Application('Atomino', '1');
	}

	public function addCliModule(CliModule $cliModule):static{
		$this->application->addCommands($cliModule->getCommands());
		return $this;
	}

	public function addCommand(Command $command):static{
		$this->application->add($command);
		return $this;
	}

	public function run(): void { $this->application->run(); }
}