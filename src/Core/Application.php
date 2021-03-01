<?php namespace Atomino\Core;

use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Composer\Autoload\ClassLoader;
use DI\Container;
use DI\ContainerBuilder;


class Application{

	private static Container $DIC;
	private static Environment $ENV;
	private static array $CFG;
	private static null|Application $instance = null;

	public static function DIC():Container{return static::$DIC;}
	public static function ENV():Environment{return static::$ENV;}
	public static function CFG():array{return static::$CFG;}

	public final function __construct(string $root, ClassLoader $classLoader, array $config, array $di){
		// Create Application instance
		if(!is_null(static::$instance)) throw new \Exception('Only one '.self::class.' instance allowed!');
		static::$instance = $this;

		// Create Environment
		static::$ENV = new Environment($root, $classLoader);

		// Load config
		static::$CFG = $config;

		// Build DI Container
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		$builder->addDefinitions($di);
		static::$DIC = $builder->build();

		// Start runner
		if (static::ENV()->isCli()) static::DIC()->get(CliRunnerInterface::class)->run();
		if (static::ENV()->isWeb()) static::DIC()->get(HttpRunnerInterface::class)->run();
	}

}