<?php namespace Atomino\Cli;

use Atomino\Core\Runner\CliRunnerInterface;
use Symfony\Component\Console\Application;

abstract class CliRunner implements CliRunnerInterface{

	private Application $application;

	public function __construct(){
		$this->application = new Application('Atomino', '1');
		$this->setup();
	}

	abstract function setup();

	public function getApplication(): Application{ return $this->application; }

	public function run():void{ $this->application->run(); }
}