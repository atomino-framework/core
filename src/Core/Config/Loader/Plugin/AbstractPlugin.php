<?php namespace Atomino\Core\Config\Loader\Plugin;


abstract class AbstractPlugin {
	abstract public function getCode():string;
	abstract public function process($value):mixed;
}
