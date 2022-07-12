<?php namespace Atomino\Core\Config\Loader\Plugin;


class EnvBoolPlugin extends AbstractPlugin {
	public function getCode(): string { return 'env-bool'; }
	public function process($value): mixed { return boolval(getenv($value)); }
}