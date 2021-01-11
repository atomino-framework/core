<?php namespace Atomino\Cli;

use Atomino\Cli\Attributes\Command;

class CliModule{

	static function addToRunner(CliRunner $runner, mixed $config = null){

		$methods = ( new \ReflectionClass(get_called_class()) )->getMethods();

		$instance = new static();

		foreach ($methods as $method){
			if (!is_null($_command = Command::get($method))){
				/** @var \Atomino\Cli\CliCommand $command */
				$command = $instance->{$method->getName()}();
				$command->setConfig($config);
				$command->setName($_command->getName());
				if (!is_null($_command->getAlias())) $command->setAliases([$_command->getAlias()]);
				if (!is_null($_command->getDescription())) $command->setDescription($_command->getDescription());
				$runner->getApplication()->add($command);
			}
		}
	}

}