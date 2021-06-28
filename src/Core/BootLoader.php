<?php namespace Atomino\Core;

use DI\Container;


/**
 * Class BootLoader
 *
 * A basic - but fair to use - implementation of the BootLoaderInterface. It can collect BootInterface implementations or callables (functions).
 *
 * @package Atomino\Core
 */
class BootLoader implements BootLoaderInterface {

	public function __construct(private Container $container) { }

	/** @var BootInterface[] */
	private array $bootSequence = [];

	/**
	 * @inheritdoc
	 */
	public function boot(): void { foreach ($this->bootSequence as $boot) $boot($this->container); }

	/**
	 * @param BootInterface|callable|bool $bootable
	 * @return $this
	 */
	public function add(BootInterface|callable|bool $bootable) {
		if (is_callable($bootable)) $this->bootSequence[] = $bootable;
		return $this;
	}

}

