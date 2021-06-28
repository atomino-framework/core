<?php namespace Atomino\Core;

use Atomino\Core\Config\ConfigInterface;
use Atomino\Core\Runner\RunnerInterface;
use DI\Container;
use DI\ContainerBuilder;

class_alias(ConfigInterface::class, ApplicationConfig::class);


/**
 * Class Application
 *
 * Entry point of any Atomino application.
 *
 * @package Atomino\Core
 */
class Application implements PathResolverInterface {

	private Container $container;
	private static self|null $instance = null;
	private string $requestId;

	const MODE_DEV = false;
	const MODE_PROD = true;

	/**
	 * Application constructor. Application is a singleton, it can be instantiated once.
	 *
	 * @param callable|string $diLoader it be a function (it will get a \DI\Builder as argument to create the DI) or a string which is a glob pattern to di definition files to be loaded.
	 * @param string|null $compiledContainer where to generate the compied container
	 * @param bool $mode false = development mode, true = production mode
	 * @param string $root project root
	 * @param string|null $bootLoader BootLoader class - it must implement the BootLoaderInterface
	 * @param string $runner Runner class - it must implement the RunnerInterface
	 * @throws \DI\DependencyException
	 * @throws \DI\NotFoundException
	 */
	public function __construct(
		callable|string $diLoader,
		private string|null $compiledContainer,
		private bool $mode,
		private string $root,
		string|null $bootLoader,
		string $runner
	) {
		if (!is_null(static::$instance)) throw new \Exception("Application can be instantiated once!");
		static::$instance = $this;
		$this->root = realpath($this->root);
		$this->requestId = uniqid();
		$this->container = $this->loadDI(is_string($diLoader) ? static::createDIContainerBuilder($diLoader) : $diLoader);

		if (!is_null($bootLoader)) (fn(BootLoaderInterface $bootLoader) => $bootLoader->boot())($this->container->get($bootLoader));

		(fn(RunnerInterface $runner) => $runner->run()) ($this->container->get($runner));
	}

	private function loadDI(callable $diLoader) {
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if (!is_null($this->compiledContainer)) $builder->enableCompilation(dirname($this->compiledContainer), pathinfo($this->compiledContainer, PATHINFO_FILENAME));
		if (is_null($this->compiledContainer) || (!is_null($this->compiledContainer) && !file_exists($this->compiledContainer))) $diLoader($builder);
		return $builder->build();
	}

	/**
	 * @param string $path
	 * @return string an absolute path based on the project root and the given path
	 */
	public function path(string $path = ''): string { return $this->root . ($path !== '' ? '/' . trim($path, '/') : ''); }

	/**
	 * @return static the application singleton
	 */
	public static function instance(): static { return static::$instance; }

	/**
	 * @return bool is the application runs in development mode
	 */
	public static function isDev(): bool { return static::$instance->mode === static::MODE_DEV; }

	/**
	 * @return bool is the application runs in production mode
	 */
	public static function isProd(): bool { return static::$instance->mode === static::MODE_PROD; }

	/**
	 * @return string the path of the compiled container
	 */
	public static function dicc(): null|string { return static::$instance->compiledContainer; }

	/**
	 * @return string every time you create an appication it will get a unique id
	 */
	public static function requestId(): string { return static::$instance->requestId; }

	/**
	 * Creates a callable function, based on the glob argument given
	 * @param string $glob the pattern to find the di definitions
	 * @return callable the loader
	 */
	public static function createDIContainerBuilder(string $glob): callable { return fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...glob($glob)); }

	/**
	 * Returns the di container. do not use this method.
	 * @return Container
	 */
	public static function getContainer(): Container { return static::$instance->container; }
}
