<?php namespace Atomino\Core\Config\Loader\Plugin;


class EnvNumPlugin extends AbstractPlugin {
	public function getCode(): string { return 'env-num'; }
	public function process($value): mixed { return floatval(getenv($value)); }
}