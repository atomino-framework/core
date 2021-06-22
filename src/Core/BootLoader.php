<?php namespace Atomino\Core;

use DI\Container;

class BootLoader implements BootLoaderInterface {

	public function __construct(private Container $container) {}

	/** @var BootInterface[]  */
	private array $bootSequence = [];

	public function boot() {
		foreach ($this->bootSequence as $boot)$boot($this->container);
	}

	public function add(BootInterface|callable|bool $bootable){
		if(is_callable($bootable)) $this->bootSequence[] = $bootable;
		return $this;
	}

}