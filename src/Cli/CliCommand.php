<?php namespace Atomino\Cli;

use Atomino\Cli\Exceptions\Error;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class CliCommand extends Command{

	protected Style $style;
	protected InputInterface $input;
	protected OutputInterface $output;
	protected mixed $config;

	public function setConfig(mixed $config): void{ $this->config = $config; }

	protected final function execute(InputInterface $input, OutputInterface $output):int{
		$this->style = new Style($input, $output);
		$this->input = $input;
		$this->output = $output;
		try{
			$message = $this->exec($this->config) ?? 'OK';
			$this->style->newLine();
			$this->style->writeln('<fg=green;options=bold>' . $message . '</>');
		}catch (Error $e){
			$this->style->newLine();
			$this->style->writeln('<fg=red;options=bold>' . ($e->getMessage() ?: 'terminated') . '</>');
			$this->style->newLine();
			return 1;
		}
		return 0;
	}

	abstract protected function exec(mixed $config);
}