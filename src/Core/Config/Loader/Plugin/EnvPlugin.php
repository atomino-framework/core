<?php namespace Atomino\Core\Config\Loader\Plugin;


class EnvPlugin extends AbstractPlugin {
	public function getCode(): string { return 'env'; }
	public function process($value): mixed { return getenv($value); }
}