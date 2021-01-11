<?php namespace Atomino\Core;

use Atomino\Core\Environment\Environment;
use Atomino\Core\Environment\EnvironmentInterface;
use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Composer\Autoload\ClassLoader;
use DI\Container;


abstract class Application{

	private static Container $DIC;
	private static EnvironmentInterface $ENV;
	private static null|Application $instance = null;

	public static function DIC():Container{return static::$DIC;}
	public static function ENV():EnvironmentInterface{return static::$ENV;}

	public final function __construct(string $root, ClassLoader $classLoader){
		if(!is_null(static::$instance)) throw new \Exception('Only one '.self::class.' instance allowed!');
		static::$instance = $this;
		static::$ENV = $this->createEnvironment($root, $classLoader);
		static::$DIC = $this->createDIC();
		$this->exec();
	}

	protected function createEnvironment(string $root, ClassLoader $classLoader):EnvironmentInterface{
		return new Environment($root, $classLoader);
	}

	protected function exec(){
		if (static::ENV()->isCli()) static::DIC()->get(CliRunnerInterface::class)->run();
		if (static::ENV()->isWeb()) static::DIC()->get(HttpRunnerInterface::class)->run();
	}

	abstract protected function createDIC():Container;
}