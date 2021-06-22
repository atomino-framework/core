<?php namespace Atomino;

use Atomino\Core\Application;
use Atomino\Core\Debug\DebugHandler;
use Atomino\Neutrons\Path;
use Symfony\Component\EventDispatcher\EventDispatcher;

if (!function_exists('Atomino\path')) {
	function path(string $path = ''): string { return rtrim(getenv("@root"),'/') . '/' . ltrim($path, '/'); }
}
//
//if (!function_exists('Atomino\dic')) {
//	function dic(): \DI\Container { return Application::getContainer(); }
//}

//if (!function_exists('Atomino\readenv')) {
//	function loadenv(string $file) {
//		if (file_exists($file)) {
//			$env = parse_ini_file($file, false, INI_SCANNER_TYPED);
//			foreach ($env as $key => $value){
//				if(str_ends_with($key,' path')){
//					$value = path($value);
//					$key = substr($key, 0, -5);
//				}
//				putenv($key . "=" . $value);
//			}
//		}
//	}
//}
//
//if (!function_exists('Atomino\readini')) {
//	function readini(string $file): array {
//		$array = [];
//		if (!file_exists($file)) return [];
//		$ini = parse_ini_file($file, false, INI_SCANNER_TYPED);
//
//		array_walk($ini, function ($value, $key) use (&$array) {
//			if(str_ends_with($key,' path')){
//				$value = path($value);
//				$key = substr($key, 0, -5);
//			}
//			$keys = explode('.', $key);
//			while (count($keys) > 1) {
//				$key = array_shift($keys);
//				if (!isset($array[$key]) || !is_array($array[$key])) $array[$key] = [];
//				$array = &$array[$key];
//			}
//			$array[array_shift($keys)] = $value;
//		});
//		return $array;
//	}
//}
//
//if (!function_exists('Atomino\cfg')) {
//	function cfg(string|null $key = null): mixed { return Application::getConfig($key); }
//}

//if (!function_exists('Atomino\settings')) {
//	function settings(string|null $key = null): mixed { return Application::getConfig("settings." . $key); }
//}

if (!function_exists('Atomino\inject')) {
	function inject(object $object, string $property, mixed $value) {
		\Closure::bind(function ($property, $value) { $this->$property = $value; }, $object, get_class($object))($property, $value);
	}
}

if (!function_exists('Atomino\debug')) {
	function debug(mixed $data, string $channel = DebugHandler::DEBUG_DUMP) { 
		Application::getContainer()->has(DebugHandler::class) && Application::getContainer()->get(DebugHandler::class)->handle($data, $channel);
	}
}

//if (!function_exists('Atomino\alert')) {
//	function alert(mixed $data) { \Atomino\debug($data, DebugHandler::DEBUG_ALERT); }
//}
