<?php namespace Atomino\RequestPipeline\Responder\Smart;

use JetBrains\PhpStorm\ArrayShape;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;

class SmartResponderEnv{

	private array $env = [];

	public function __construct(
		private ?string $twigCacheDir = null,
		private string $frontendVersion = '0',
		private bool $debug = false,
		private array $namespaces = []
	){
		foreach ($this->namespaces as $namespace=>$path) $this->addEnv($namespace);
	}
	
	public function addEnv(string $name, string $frontendRoot=null, string $main=null):static{
		$frontendRoot = $frontendRoot ?? '/~'.$name.'/';
		$main = $main ?? $name;
		$this->env[$name] = compact('frontendRoot', 'main');
		return $this;
	}

	#[ArrayShape(["twigCache"=>CacheInterface::class, "frontendVersion"=>"string", "debug"=>bool, "namespaces"=>"array", "frontendRoot"=>"string"])]
	public function getEnv($name){
		$env = $this->env[$name];
		$env['twigCache'] = $this->getTwigCache();
		$env['debug'] = $this->debug;
		$env['frontendVersion'] = $this->frontendVersion;
		$env['namespaces'] = $this->getNamespaces($env['main']);
		return $env;
	}

	private function getTwigCache(): ?CacheInterface{ return $this->twigCacheDir ? new FilesystemCache($this->twigCacheDir) : null; }
	protected function getNamespaces(?string $main = null): array{
		$namespaces = $this->namespaces;
		if(!is_null($main)) $namespaces['__main__'] = $this->namespaces[$main];
		return $namespaces;
	}
}