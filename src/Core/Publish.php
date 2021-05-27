<?php namespace Atomino\Core;

use Atomino\Cli\Attributes\Command;
use Atomino\Cli\CliCommand;
use Atomino\Cli\CliModule;
use Atomino\Carbon\Generator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use function Atomino\cfg;

class Publish extends CliModule {

	#[Command('publish')]
	public function entity(): CliCommand {
		return (new class() extends CliCommand {
			protected function exec(mixed $config) {
				$this->style->_task("Clean up");
				$di = new \RecursiveDirectoryIterator(cfg("publish.public"), \FilesystemIterator::SKIP_DOTS);
				$ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
				foreach ($ri as $file) $file->isDir() ? rmdir($file) : unlink($file);
				$this->style->_task_ok();

				$this->style->_task("Copy assets to public directory");
				foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(cfg("publish.assets"), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
					if ($item->isDir()) mkdir(cfg("publish.public") . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					else copy($item, cfg("publish.public") . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
				}
				$this->style->_task_ok();
			}
		});
	}
}