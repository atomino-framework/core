<?php namespace Atomino;

use Atomino\Core\Application;

if (! function_exists('Atomino\path')) {
	function path($path) { return realpath(__DIR__ . '/../../../../') . ltrim($path, '/'); }
}