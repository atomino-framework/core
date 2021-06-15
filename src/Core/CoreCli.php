<?php namespace Atomino\Core;

use Atomino\Core\Cli\Attributes\Command;
use Atomino\Core\Cli\CliCommand;
use Atomino\Core\Cli\CliModule;
use Atomino\Carbon\Generator\Generator;
use Atomino\Core\Cli\ConsoleTree;
use Symfony\Component\Console\Input\InputArgument;
use function Atomino\cfg;

class CoreCli extends CliModule {

	#[Command("clear-caches", "cc", "Clears the config and DI cache")]
	public function clearCaches():CliCommand{
		return (new class() extends CliCommand {
			protected function exec(mixed $config){
				$this->style->_task("Clear DI cache");
				if($file = Application::getDIFile()) unlink($file);
				$this->style->_task_ok();

				$this->style->_task("Clear config cache");
				if($file = Application::getConfigFile()) unlink($file);
				$this->style->_task_ok();
			}
		});
	}


	#[Command("show-config", "cfg", "Shows the config")]
	public function showConfig():CliCommand{
		return (new class() extends CliCommand {
			protected function exec(mixed $config){
				ConsoleTree::draw(cfg(), $this->style, 'cfg');
			}
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