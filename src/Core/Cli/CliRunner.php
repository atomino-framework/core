<?php namespace Atomino\Core\Cli;

use Atomino\Core\Runner\CliRunnerInterface;
use Symfony\Component\Console\Application;

class CliRunner implements CliRunnerInterface {

	private Application $application;

	public function __construct() {
		$this->application = new Application('Atomino', '1');
	}

	public function addCliModule(CliModule $cliModule):static{
		$this->application->addCommands($cliModule->getCommands());
		return $this;
	}

	public function run(): void { $this->application->run(); }
}