<?php namespace Atomino\Core\Config;

interface ProviderInterface {
	public function __invoke(): array;
}
