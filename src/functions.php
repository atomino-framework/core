<?php namespace Atomino;

use Atomino\Core\Application;

if (!function_exists('Atomino\path')) {
	if (!getenv('ROOT')) putenv("ROOT=" . realpath(__DIR__ . '/../../../..'));
	function path(string $path = ''): string { return getenv("ROOT") . '/' . ltrim($path, '/'); }
}

if (!function_exists('Atomino\dic')) {
	function dic(): \DI\Container { return Application::DIC(); }
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