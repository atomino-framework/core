<?php namespace Atomino\RequestPipeline\Responder\Smart\Attributes;

use Atomino\RequestPipeline\Responder\Smart\SmartResponderEnv;
use Atomino\Neutrons\Attr;
use Attribute;
use Twig\Cache\CacheInterface;
use function Atomino\dic;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Init extends Attr{
	public string $frontendVersion;
	public array $namespaces;
	public CacheInterface $twigCache;
	public string $frontendRoot;
	public string $template;
	public bool $debug;

	public function __construct(string $environment, string $template){
		$env = dic()->get(SmartResponderEnv::class)->getEnv($environment);
		$this->frontendVersion = $env['frontendVersion'];
		$this->namespaces = $env['namespaces'];
		$this->twigCache = $env['twigCache'];
		$this->debug = $env['debug'];
		$this->frontendRoot = $env['frontendRoot'];
		$this->template = $template;
	}
}
