<?php namespace Atomino\Core\Cli;

use Atomino\Core\Cli\Attributes\Command;

class CliModule {

	private array $commands = [];

	public function __construct($config = null) {
		$methods = (new \ReflectionClass(get_called_class()))->getMethods();
		foreach ($methods as $method) {
			if (!is_null($_command = Command::get($method))) {
				/** @var \Atomino\Core\Cli\CliCommand $command */
				$command = $this->{$method->getName()}();
				$command->setConfig($config);
				$command->setName($_command->getName());
				if (!is_null($_command->getAlias())) $command->setAliases([$_command->getAlias()]);
				if (!is_null($_command->getDescription())) $command->setDescription($_command->getDescription());
				$this->commands[] = $command;
			}
		}
	}

	public function getCommands() { return $this->commands; }

}