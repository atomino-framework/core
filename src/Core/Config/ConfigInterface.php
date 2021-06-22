<?php


namespace Atomino\Core\Config;


interface ConfigInterface {
	public function __invoke(string $key):mixed;
	public function all():array;
}