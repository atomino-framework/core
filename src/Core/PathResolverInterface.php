<?php namespace Atomino\Core;

interface PathResolverInterface {
	public function path(string $path = ''):string;
}