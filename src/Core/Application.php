<?php namespace Atomino\Core;

use Atomino\Core\Runner\CliRunnerInterface;
use Atomino\Core\Runner\HttpRunnerInterface;
use DI\Container;
use DI\ContainerBuilder;


class Application {

	private static Container $diContainer;
	private static array $config;

	public static function setConfig(array $config) {
		if (isset(static::$config)) throw new \Exception("Atomino config, has been set already");
		static::$config = $config;
	}

	public static function setDI(array $di) {
		if (is_null(static::$config)) throw new \Exception("Atomino config, has not been set");
		if (isset(static::$diContainer)) throw new \Exception("Atomino DI, has been set already");
		$builder = new ContainerBuilder();
		$builder->useAutowiring(true);
		$builder->addDefinitions($di);
		static::$diContainer = $builder->build();
	}

	public static function boot() {
		if (is_null(static::$diContainer)) throw new \Exception("Atomino DI, has not been set");
		if (static::$diContainer->has(BootInterface::class)) static::$diContainer->get(BootInterface::class)->boot();
		static::$diContainer->get(http_response_code() ? HttpRunnerInterface::class : CliRunnerInterface::class)->run();
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