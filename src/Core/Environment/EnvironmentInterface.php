<?php namespace Atomino\Core\Environment;

use Composer\Autoload\ClassLoader;

interface EnvironmentInterface{
	public function getClassLoader():ClassLoader;
	public function getRoot():string;
	public function isWeb():bool;
	public function isCli():bool;
	public function isDev():bool;
	public function isProd():bool;
}

