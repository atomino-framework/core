<?php namespace Atomino\Core;

use DI\Container;

interface BootLoaderInterface {
	/**
	 * This can be called in the \Atomino\Core\Application
	 */
	public function boot():void;
}