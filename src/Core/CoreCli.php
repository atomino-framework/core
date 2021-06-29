<?php namespace Atomino\Core;

use Atomino\Core\Cli\Attributes\Command;
use Atomino\Core\Cli\CliCommand;
use Atomino\Core\Cli\CliModule;
use Atomino\Core\Cli\CliTree;
use Atomino\Core\Cli\ConsoleTree;
use Atomino\Core\Cli\Style;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use function Atomino\cfg;
use function Atomino\dic;

class CoreCli extends CliModule {

	public function __construct(private ApplicationConfig $config) { }

	#[Command("clear-caches", "cc", "Clears the config and DI cache")]
	public function clearCaches(CliCommand $command) {
		$command->define(function (Input $input, Output $output, Style $style) {
			$style->_task("Delete DI compiled container");
			if (($file = Application::dicc()) && file_exists($file)) {
				unlink($file);
				$style->_task_ok();
			}else{
				$style->_task_warn("does not exists");
			}
		});
	}

	#[Command("show-config", "cfg", "Shows the config")]
	public function showConfig(CliCommand $command) {
		$command->define(function (Input $input, Output $output, Style $style) {
			echo CliTree::draw($this->config->all(),'cfg');
		});
	}

//	#[Command('publish')]
//	public function entity(): CliCommand {
//		return (new class() extends CliCommand {
//			protected function exec(mixed $config) {
//				$this->style->_task("Clean up");
//				if(!is_dir(cfg("publish.public"))) mkdir(cfg("publish.public"), 0777, true);
//				$di = new \RecursiveDirectoryIterator(cfg("publish.public"), \FilesystemIterator::SKIP_DOTS);
//				$ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
//				foreach ($ri as $file) $file->isDir() ? rmdir($file) : unlink($file);
//				$this->style->_task_ok();
//
//				$this->style->_task("Copy assets to public directory");
//				foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(cfg("publish.assets"), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
//					if ($item->isDir()) mkdir(cfg("publish.public") . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
//					else copy($item, cfg("publish.public") . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
//				}
//				$this->style->_task_ok();
//			}
//		});
//	}
}