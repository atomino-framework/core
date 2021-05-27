<?php namespace Atomino\Core;

use Atomino\Cli\Attributes\Command;
use Atomino\Cli\CliCommand;
use Atomino\Cli\CliModule;
use Atomino\Carbon\Generator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use function Atomino\cfg;

class ConfigCache extends CliModule {

	#[Command('config-cache')]
	public function entity(): CliCommand {
		return (new class() extends CliCommand {
			protected function exec(mixed $config) {
				$this->style->_task("Creating config cache");
				$cfg = include cfg("publish.config");
				file_put_contents(cfg('publish.config-cache'), '<?php return '.var_export($cfg, true).';');
				$this->style->_task_ok();
			}
		});
	}
}