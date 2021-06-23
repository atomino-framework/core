<?php namespace Atomino\Core;

use Atomino\Core\Config\ConfigInterface;
use DI\Container;
use DI\ContainerBuilder;

class_alias(ConfigInterface::class, ApplicationConfig::class);

class Application implements PathResolverInterface {

	private Container $container;
	private static self|null $instance = null;

	const MODE_DEV = false;
	const MODE_PROD = true;

	public function __construct(
		callable|string $diLoader,
		private string|null $compiledContainer,
		private bool $mode,
		private string $root,
		string|null $bootLoader,
		string $runner
	) {
		$this->root = realpath($this->root);
		if (!is_null(static::$instance)) throw new \Exception("Application can be instantiated once!");
		static::$instance = $this;
		$this->container = $this->loadDI(is_string($diLoader) ? static::createDIContainerBuilder($diLoader) : $diLoader);
		if (!is_null($bootLoader)) $this->container->get($bootLoader)->boot();
		$this->container->get($runner)->run();
	}

	private function loadDI(callable $diLoader) {
		if (is_null($this->compiledContainer)) $this->mode = self::MODE_DEV;
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		if ($this->mode === self::MODE_PROD) $builder->enableCompilation(dirname($this->compiledContainer), pathinfo($this->compiledContainer, PATHINFO_FILENAME));
		if ($this->mode === self::MODE_DEV || !file_exists($this->compiledContainer)) $diLoader($builder);
		return $builder->build();
	}

	public function path(string $path = ''): string { return $this->root . ($path !== '' ? '/' . trim($path, '/') : ''); }
	public static function instance(): static { return static::$instance; }
	public static function isDev(): bool { return static::$instance->mode === static::MODE_DEV; }
	public static function isProd(): bool { return static::$instance->mode === static::MODE_PROD; }
	public static function dicc(): string { return static::$instance->compiledContainer; }

	public static function createDIContainerBuilder($glob): callable { return fn(\DI\ContainerBuilder $builder) => $builder->addDefinitions(...glob($glob)); }
	public static function getContainer(): Container { return static::$instance->container; }

}