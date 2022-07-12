<?php namespace Atomino\Core\Config;

use Atomino\Core\Config\Loader\AbstractLoader;

class Loader {
	/** @var AbstractLoader[] */
	private array $loaders;

	public function __construct(AbstractLoader ...$loaders) {
		$this->loaders = $loaders;
	}

	public function load(...$files): ProviderInterface {
		return new Aggregator(...array_map(fn($file) => $this->loadFile($file), $files));
	}

	private function loadFile($file) {
		foreach ($this->loaders as $loader) {
			if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === $loader->getExtension()) return $loader->load($file);
		}
		return [];
	}

}
