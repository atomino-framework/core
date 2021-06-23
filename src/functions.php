<?php namespace Atomino;

use Atomino\Core\Debug\DebugHandler;
use Atomino\Core\Debug\DebugProxy;
use Atomino\Neutrons\Path;

if (!function_exists('Atomino\inject')) {
	function inject(object $object, string $property, mixed $value) {
		\Closure::bind(function ($property, $value) { $this->$property = $value; }, $object, get_class($object))($property, $value);
	}
}

if (!function_exists('Atomino\debug')) {
	function debug(mixed $data, string $channel = DebugHandler::DEBUG_DUMP) {
		DebugProxy::debug($data, $channel);
	}
}
