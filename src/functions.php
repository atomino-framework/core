<?php namespace Atomino;

use Atomino\Core\Application;
use Atomino\Debug\Debug;
use Symfony\Component\EventDispatcher\EventDispatcher;

if (!function_exists('Atomino\path')) {
	if (!getenv('@root')) putenv("@root=" . realpath(__DIR__ . '/../../../..'));
	function path(string $path = ''): string { return rtrim(getenv("@root"),'/') . '/' . ltrim($path, '/'); }
}

if (!function_exists('Atomino\dic')) {
	function dic(): \DI\Container { return Application::getDIContainer(); }
}

if (!function_exists('Atomino\readenv')) {
	function loadenv(string $file) {
		if (file_exists($file)) {
			$env = parse_ini_file($file, false, INI_SCANNER_TYPED);
			foreach ($env as $key => $value) putenv($key . "=" . $value);
		}
	}
}

if (!function_exists('Atomino\readini')) {
	function readini(string $file): array {
		$array = [];
		if (!file_exists($file)) return [];
		$ini = parse_ini_file($file, false, INI_SCANNER_TYPED);

		array_walk($ini, function ($value, $key) use (&$array) {
			if(str_ends_with($key,':path')){
				$value = path($value);
				$key = substr($key, 0, -5);
			}
			$keys = explode('.', $key);
			while (count($keys) > 1) {
				$key = array_shift($keys);
				if (!isset($array[$key]) || !is_array($array[$key])) $array[$key] = [];
				$array = &$array[$key];
			}
			$array[array_shift($keys)] = $value;
		});
		return $array;
	}
}

if (!function_exists('Atomino\cfg')) {
	function cfg(string|null $key = null): mixed { return Application::getConfig($key); }
}

if (!function_exists('Atomino\settings')) {
	function settings(string|null $key = null): mixed { return Application::getConfig("settings." . $key); }
}

if (!function_exists('Atomino\inject')) {
	function inject(object $object, string $property, mixed $value) {
		\Closure::bind(function ($property, $value) { $this->$property = $value; }, $object, get_class($object))($property, $value);
	}
}

if (!function_exists('Atomino\debug')) {
	function debug(mixed $data, string $channel = Debug::DEBUG_DUMP) { dic()->has(Debug::class) && dic()->get(Debug::class)->handle($data, $channel); }
}

if (!function_exists('Atomino\alert')) {
	function alert(mixed $data) { \Atomino\debug($data, Debug::DEBUG_ALERT); }
}
