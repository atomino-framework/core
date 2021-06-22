<?php namespace Atomino\Core\Config\Loader;

use Atomino\Core\Config\ProviderInterface;

class PhpLoader extends AbstractLoader {
	public function getExtension(): string { return "php"; }
	public function load($file): array { return require $file; }
}