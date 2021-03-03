<?php namespace Atomino;

use Atomino\Core\Application;
use Atomino\Core\Environment;
use DI\Container;

if (!function_exists('Atomino\path')) {
	if (!getenv('ROOT')) putenv("ROOT=" . realpath(__DIR__ . '/../../../..'));
	function path(string $path = ''): string { return getenv("ROOT") . '/' . ltrim($path, '/'); }
}

if (!function_exists('Atomino\dic')) {
	function dic(): Container { return Application::DIC(); }
}

if (!function_exists('Atomino\cfg')) {
	function cfg(string $key): mixed { return Application::cfg($key); }
}

if (!function_exists('Atomino\settings')) {
	function settings(string $key): mixed { return Application::cfg("settings.".$key); }
}