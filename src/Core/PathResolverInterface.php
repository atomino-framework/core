<?php namespace Atomino\Core;


interface PathResolverInterface {
	/**
	 * Must return an absolute path based on project root, and the path given
	 * @param string $path
	 * @return string
	 */
	public function path(string $path = ''):string;
}