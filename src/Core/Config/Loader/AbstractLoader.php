<?php namespace Atomino\Core\Config\Loader;

use Atomino\Core\Config\Loader\Plugin\AbstractPlugin;
use Atomino\Core\Config\ProviderInterface;

abstract class AbstractLoader {
	abstract public function getExtension():string;
	abstract public function load(string $file):array;
}