<?php namespace Atomino\Responder\SmartResponder;

use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;


class SmartResponderEnv{

	private array $namespaces = [];
	private int $frontendVersion = 0;
	private array $env = [];

	public function __construct(
		private ?string $twigCacheDir = null,
		?string $frontendVersionFile = null,
	){
		if (!is_null($frontendVersionFile)){
			if (!file_exists($frontendVersionFile)) file_put_contents($frontendVersionFile, '0');
			$this->frontendVersion = intval(file_get_contents($frontendVersionFile));
		}
	}
	public function addNamespace(string $namespace, string $path):static{ $this->namespaces[$namespace] = $path; return $this; }

	public function addEnv(string $name, string $frontendRoot=null, string $main=null):static{
		$frontendRoot = $frontendRoot ?? '/~'.$name.'/';
		$main = $main ?? $name;
		$this->env[$name] = compact('frontendRoot', 'main');
		return $this;
	}

	public function getEnv($name){
		$env = $this->env[$name];
		$env['twigCache'] = $this->getTwigCache();
		$env['frontendVersion'] = $this->frontendVersion;
		$env['namespaces'] = $this->getNamespaces($env['main']);
		return $env;
	}

	private function getTwigCache(): ?CacheInterface{ return $this->twigCacheDir ? new FilesystemCache($this->twigCacheDir) : null; }
	protected function getNamespaces(?string $main = null): array{
		$namespaces = $this->namespaces;
		$namespaces['__main__'] = $this->namespaces[$main];
		return $namespaces;
	}
}