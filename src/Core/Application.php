<?php namespace Atomino\Core;

use Atomino\Core\Config\ConfigInterface;
use Atomino\Core\Debug\ErrorHandlerInterface;
use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use Atomino\Core\Runner\RunnerInterface;
use DI\Container;
use DI\ContainerBuilder;
use function Atomino\cfg;
use function Atomino\readini;

class Application {

	private Container $container;
	private static Application $instance;

	const MODE_DEV = "dev";
	const MODE_PROD = "prod";

	public function __construct(
		callable|string|false $diLoader = false,
		string|bool $compiledContainer = false,
		string|bool $mode = false,
		string|bool $root = false
	) {
		static::$instance = $this;
		if ($diLoader === false) $diLoader = getenv("@di") . '/*.php';
		if (is_string($diLoader)) $diLoader = Application::createDIContainerBuilder($diLoader);

		if ($compiledContainer === false) $compiledContainer = getenv("@dicc");
		else putenv("@dicc=" . $compiledContainer);

		if ($mode !== false) putenv("@mode=" . $mode);
		if (getenv("@mode" !== self::MODE_PROD)) putenv("@mode=" . self::MODE_DEV);

		if ($root !== false) putenv("@root=" . $root);

		$this->container = $this->loadDI(getenv("@mode"), $diLoader, $compiledContainer);

		$this->container->get(BootLoaderInterface::class)->boot();
	}

	private function loadDI(string $mode, callable $loader, string|bool $compiledContainer) {
		if (!$compiledContainer) $mode = self::MODE_DEV;
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if ($mode === self::MODE_PROD) $builder->enableCompilation(dirname($compiledContainer), pathinfo($compiledContainer, PATHINFO_FILENAME));
		if ($mode === self::MODE_DEV || !file_exists($compiledContainer)) $loader($builder);
		return $builder->build();
	}

	public function run(string $runnner) { $this->container->get($runnner)->run(); }

	public static function isDev(): bool { return getenv("@mode") === static::MODE_DEV; }
	public static function isProd(): bool { return getenv("@mode") === static::MODE_PROD; }
	public static function createDIContainerBuilder($glob): callable { return fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...glob($glob)); }
	public static function getContainer(): Container { return static::$instance->container; }

}