<?php namespace Atomino;

use Atomino\Core\Application;

if (!function_exists('Atomino\path')) {
	if (!getenv('ROOT')) putenv("ROOT=" . realpath(__DIR__ . '/../../../..'));
	function path(string $path = ''): string { return getenv("ROOT") . '/' . ltrim($path, '/'); }
}

if (!function_exists('Atomino\dic')) {
	function dic(): \DI\Container { return Application::DIC(); }
}

if (!function_exists('Atomino\readini')) {
	function readini($file): array {
		$array = [];
		if (!file_exists($file)) return [];
		$ini = parse_ini_file($file, false, INI_SCANNER_TYPED);

		array_walk($ini, function ($value, $key) use (&$array) {
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
	function cfg(string|null $key = null): mixed { return Application::cfg($key); }
}

if (!function_exists('Atomino\settings')) {
	function settings(string|null $key = null): mixed { return Application::cfg("settings." . $key); }
}

if (!function_exists('Atomino\inject')) {
	function inject(object $object, string $property, mixed $value) {
		\Closure::bind(function ($property, $value) { $this->$property = $value; }, $object, get_class($object))($property, $value);
	}
}
