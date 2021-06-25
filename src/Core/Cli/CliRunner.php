<?php namespace Atomino\Core\Cli;

use Atomino\Core\Runner\CliRunnerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use function Atomino\debug;

class CliRunner implements CliRunnerInterface {

	const DEBUG_CHANNEL_CLI_REQUEST = 'CLI';

	private Application $application;

	public function __construct() {
		debug($_SERVER['argv'],self::DEBUG_CHANNEL_CLI_REQUEST);
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