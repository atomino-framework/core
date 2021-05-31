<?php namespace Atomino\Core;

use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Atomino\Debug\ErrorHandler;
use Composer\Autoload\ClassLoader;
use DI\Container;
use DI\ContainerBuilder;
use mysql_xdevapi\Exception;
use function Atomino\dic;
use function Atomino\path;


class Application {

	private static Container $DIC;
	private static array $cfg;
	private static null|Application $instance = null;
	private static int $context;

	const CONTEXT_CLI = 1;
	const CONTEXT_WEB = 2;

	public final function __construct(array $config, array $di) {

		static::$context = (http_response_code() ? static::CONTEXT_WEB : self::CONTEXT_CLI);

		// Set Application instance
		if (!is_null(static::$instance)) throw new \Exception('Only one ' . self::class . ' instance allowed!');
		static::$instance = $this;

		// Load config
		static::$cfg = $config;

		// Build DI Container
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		$builder->addDefinitions($di);
		static::$DIC = $builder->build();

		// Setup Error Handler
		if (static::$DIC->has(BootInterface::class)) static::$DIC->get(BootInterface::class)->boot();

		// Start runner
		if (static::isWeb()) static::DIC()->get(HttpRunnerInterface::class)->run();
		if (static::isCli()) static::DIC()->get(CliRunnerInterface::class)->run();
	}

	public static function DIC(): Container { return static::$DIC; }
	public static function isCli(): bool { return self::$context === self::CONTEXT_CLI; }
	public static function isWeb(): bool { return self::$context === self::CONTEXT_WEB; }

	public static function cfg(string|null $key = null): mixed {
		if (is_null($key)) return static::$cfg;
		$keys = explode('.', $key);
		$cfg = static::$cfg;
		foreach ($keys as $key) $cfg = $cfg[$key];
		return $cfg;
	}


}