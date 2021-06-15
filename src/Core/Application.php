<?php namespace Atomino\Core;

use Atomino\Bundle\Debug\ErrorHandler;
use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Atomino\Core\Debug\ErrorHandlerInterface;
use DI\Container;
use DI\ContainerBuilder;
use function Atomino\cfg;
use function Atomino\dic;
use function Atomino\path;
use function Atomino\readini;


class Application {

	private static Container $diContainer;
	private static array $config;
	private static string|bool $etc = false;

	const MODE_DEV = "dev";
	const MODE_PROD = "prod";

	private const DIClass = "AtominoDI";
	private const configFile = "atomino-config.php";

	private static function loadConfig(string $mode, callable $loader) {
		$store = static::$etc ? static::$etc . self::configFile : false;
		if (!$store) $mode = self::MODE_DEV;

		if ($mode === self::MODE_DEV) return $loader();
		if (!file_exists($store)) file_put_contents($store, '<?php return ' . var_export($loader(), true) . ';');
		return include $store;
	}

	private static function loadDI(string $mode, callable $loader) {
		$store = static::$etc ? static::$etc . '/' . self::DIClass . '.php' : false;
		if (!$store) $mode = self::MODE_DEV;

		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if ($mode === self::MODE_PROD) $builder->enableCompilation(self::$etc, self::DIClass);
		if ($mode === self::MODE_DEV || !file_exists($store)) $loader($builder);
		return $builder->build();
	}

	public static function boot(callable $cfgLoader, callable $diLoader, string|bool $etcPath = false) {
		if (getenv("@mode" !== self::MODE_PROD)) putenv("@mode=" . self::MODE_DEV);
		static::$etc = $etcPath;
		static::$config = static::loadConfig(getenv("@mode"), $cfgLoader);
		static::$diContainer = static::loadDI(getenv("@mode"), $diLoader);

		if (static::$diContainer->has(ErrorHandlerInterface::class)) static::$diContainer->get(ErrorHandlerInterface::class)->register();
		if (static::$diContainer->has(BootInterface::class)) static::$diContainer->get(BootInterface::class)->boot();

		static::$diContainer->get(http_response_code() ? HttpRunnerInterface::class : CliRunnerInterface::class)->run();
	}

	public static function getDIFile(): string|bool { return (self::$etc && file_exists($file = self::$etc . '/' . self::DIClass . '.php')) ? $file : false; }
	public static function getConfigFile(): string|bool { return (self::$etc && file_exists($file = self::$etc . '/' . self::configFile)) ? $file : false; }

	public static function createDILoader($glob): callable {
		return fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...glob($glob));
	}
	public static function createConfigLoader(string $glob, string|bool $ini = false): callable {
		return fn() => array_replace_recursive(
			...array_map(fn($file) => include $file, glob($glob)),
			...[$ini ? readini($ini) : []],
		);
	}

	public static function getDIContainer(): Container { return static::$diContainer; }
	public static function getConfig(string|null $key = null): mixed {
		if (is_null($key)) return static::$config;
		$keys = explode('.', $key);
		$cfg = static::$config;
		foreach ($keys as $key) $cfg = $cfg[$key];
		return $cfg;
	}

}