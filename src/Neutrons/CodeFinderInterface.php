<?php namespace Atomino\Neutrons;

interface CodeFinderInterface {
	public function Psr4ClassSeeker(string $namespace, string $pattern = '*.php'):array;
	public function Psr4FileSeeker(string $namespace, string $pattern = '*.php'):array;
	public function Psr4ResolveNamespace(string $namespace):string|null;
	public function Psr4ResolveClass(string $class):string|null;
	public function Psr4Resolve(string $name):string|null;
	public function fileSeeker(string $path, string $pattern = '*'):array ;
}