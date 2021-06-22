<?php namespace Atomino\Core;

use Atomino\Core\Config\ConfigInterface;
use Atomino\Core\Debug\ErrorHandlerInterface;
use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use DI\Container;
use DI\ContainerBuilder;
use function Atomino\cfg;
use function Atomino\readini;

class_alias(ConfigInterface::class, ApplicationConfig::class);

class Application {

	private static Container $container;
	private static string|bool $etc = false;

	const MODE_DEV = "dev";
	const MODE_PROD = "prod";


	private static function loadDI(string $mode, callable $loader, string|bool $compiledContainer) {
		if (!$compiledContainer) $mode = self::MODE_DEV;
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if ($mode === self::MODE_PROD) $builder->enableCompilation(dirname($compiledContainer), pathinfo($compiledContainer, PATHINFO_FILENAME));
		if ($mode === self::MODE_DEV || !file_exists($compiledContainer)) $loader($builder);
		return $builder->build();
	}

	public static function boot(callable|string|false $diLoader = false, string|bool $compiledContainer = false, string|bool $mode = false, string|bool $root = false) {
		if ($diLoader === false) $diLoader = getenv("@di") . '/*.php';
		if (is_string($diLoader)) $diLoader = Application::createDILoader($string);

		if ($compiledContainer === false) $compiledContainer = getenv("@dicc");
		else putenv("@dicc=" . $compiledContainer);

		if ($mode !== false) putenv("@mode=" . $mode);
		if (getenv("@mode" !== self::MODE_PROD)) putenv("@mode=" . self::MODE_DEV);

		if ($root !== false) putenv("@root=" . $root);

		static::$container = $container = static::loadDI(getenv("@mode"), $diLoader, $compiledContainer);

		if ($container->has(ErrorHandlerInterface::class)) $container->get(ErrorHandlerInterface::class)->register();
		if ($container->has(BootInterface::class)) $container->get(BootInterface::class)->boot();
		$container->get(http_response_code() ? HttpRunnerInterface::class : CliRunnerInterface::class)->run();
	}

	public static function createDIContainerBuilder($glob): callable { return fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...glob($glob)); }
	public static function getContainer(): Container { return static::$container; }

}