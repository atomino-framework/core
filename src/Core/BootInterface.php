<?php namespace Atomino\Core;

use DI\Container;

interface BootInterface {
	public function __invoke(Container $container);
}