<?php namespace Atomino\Core;


use function Atomino\debug;

class PathResolver implements PathResolverInterface {

	public function __construct(private string $root) {
	}

	/**
	 * Converts project root based paths to absolute path
	 *
	 * @param string $path
	 * @return string an absolute path based on the project root and the given path
	 */
	public function path(string $path = ''): string { return $this->root . ($path !== '' ? '/' . trim($path, '/') : ''); }
}