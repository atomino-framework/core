<?php namespace Atomino\Core;

use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Composer\Autoload\ClassLoader;
use DI\Container;
use DI\ContainerBuilder;


class Application {

	private static Container $DIC;
	private static Environment $ENV;
	private static array $cfg;
	private static null|Application $instance = null;

	public static function DIC(): Container { return static::$DIC; }
	public static function ENV(): Environment { return static::$ENV; }

	public static function cfg(string|null $key = null): mixed {
		if (is_null($key)) return static::$cfg;
		$keys = explode('.', $key);
		$cfg = static::$cfg;
		foreach ($keys as $key) $cfg = $cfg[$key];
		return $cfg;
	}
	public static function path(string $path) { return Application::ENV()->getRoot() . ltrim($path, '/'); }
	public static function cpath(string $key) { return Application::path(Application::cfg($key)); }

	public final function __construct(string $root, ClassLoader $classLoader, string $config, string $di) {

		// Create Application instance
		if (!is_null(static::$instance)) throw new \Exception('Only one ' . self::class . ' instance allowed!');
		static::$instance = $this;

		// Create Environment
		static::$ENV = new Environment($root);

		// Load config
		static::$cfg = include $config;

		// Build DI Container
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		$builder->addDefinitions(include $di, [ClassLoader::class => $classLoader]);
		static::$DIC = $builder->build();

		// Start runner
		if (static::ENV()->isCli()) static::DIC()->get(CliRunnerInterface::class)->run();
		if (static::ENV()->isWeb()) static::DIC()->get(HttpRunnerInterface::class)->run();
	}

}