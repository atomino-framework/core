<?php namespace Atomino\Core\Config\Loader\Plugin;


class PathPlugin extends AbstractPlugin {
	public function __construct(private string $path) { }
	public function getCode(): string { return 'path'; }
	public function process(mixed $value): mixed { return $this->path . '/' . $value; }
}