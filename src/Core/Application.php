<?php namespace Atomino\Core;

use Atomino\Core\Config\ConfigInterface;
use Atomino\Core\Runner\RunnerInterface;
use DI\Container;
use DI\ContainerBuilder;

class_alias(ConfigInterface::class, ApplicationConfig::class);

define("YES", "YES");
define("NO", "NO");

/**
 * Class Application
 *
 * Entry point of any Atomino application.
 *
 * @package Atomino\Core
 */
class Application {

	private Container $container;
	private static self|null $instance = null;
	public string $id;

	const MODE_DEV = "DEV";
	const MODE_PROD = "PROD";

	/**
	 * Application constructor. Application is a singleton, it can be instantiated once.
	 *
	 * @param callable|string $diLoader it be a function (it will get a \DI\Builder as argument to create the DI) or a string which is a glob pattern to di definition files to be loaded.
	 * @param string|null $compiledContainer where to generate the compied container
	 * @param string $mode DEV|PROD
	 * @param string $root project root
	 * @param string|null $bootLoader BootLoader class - it must implement the BootLoaderInterface
	 * @param string $runner Runner class - it must implement the RunnerInterface
	 * @throws \DI\DependencyException
	 * @throws \DI\NotFoundException
	 */
	public function __construct(
		private string      $root,
		private string      $env,
		callable|string     $diLoader,
		private string|null $compiledContainer,
		string|null         $bootLoader = null,
		string|null         $runner = null
	) {
		if (!is_null(static::$instance)) throw new \Exception("Application can be instantiated once!");
		static::$instance = $this;

		(new \Symfony\Component\Dotenv\Dotenv())->usePutenv()->loadEnv($env);

		$this->mode = getenv("ATOMINO_DEV_MODE") === false ? self::MODE_PROD : self::MODE_DEV;
		
		$this->root = realpath($this->root);
		putenv("ATOMINO_ROOT=" . $this->root);

		if ($this->mode === self::MODE_DEV) $this->compiledContainer = null;
		$this->container = $this->loadDI($diLoader);
		$this->id = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

		if (!is_null($bootLoader)) (fn(BootLoaderInterface $bootLoader) => $bootLoader->boot())($this->container->get($bootLoader));
		if (!is_null($runner)) (fn(RunnerInterface $runner) => $runner->run()) ($this->container->get($runner));
	}
	/**
	 * Returns the application singleton
	 *
	 * @return static the application singleton
	 */
	public static function instance(): static { return static::$instance; }


	private function loadDI(callable|string $diLoader) {
		if (is_string($diLoader)) {
			$files = (new \Atomino\Core\Config\Filter(glob($diLoader, GLOB_BRACE)))
				->exclude(Application::instance()->isDev() ? "@prod" : "@dev")
				->localOverride()
				->files()
			;
			$diLoader = fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...$files);
		}
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if (!is_null($this->compiledContainer)) $builder->enableCompilation(dirname($this->compiledContainer), pathinfo($this->compiledContainer, PATHINFO_FILENAME));
		if (is_null($this->compiledContainer) || (!is_null($this->compiledContainer) && !file_exists($this->compiledContainer))) $diLoader($builder);
		return $builder->build();
	}

	public function isDev(): bool { return $this->mode === self::MODE_DEV; }
	public function isProd(): bool { return $this->mode === self::MODE_PROD; }

//
//	/**
//	 * Filters group of config or di files based on running mode (dev/prod)
//	 * config file can end with "@dev.php" or "@prod.php"
//	 * config file can end with "@local.php" (or "@dev@local.php" or "@prod@local.php") that will override other similarry named files.
//	 *
//	 * @param string[] $files
//	 * @return string[]
//	 */
//	public function filterConfigFiles(array $files) {
//		$exclude = $this->isProd() ? "@dev" : "@prod";
//		$files = array_filter($files, static fn($item) => !str_contains($item, $exclude));
//		return array_filter($files, static function ($item) use ($files) {
//			$filename = pathinfo($item, PATHINFO_DIRNAME) . '/' . pathinfo($item, PATHINFO_FILENAME);
//			return ((array_search($filename . '@local.php', $files)) !== false) ? false : true;
//		});
//	}
}
