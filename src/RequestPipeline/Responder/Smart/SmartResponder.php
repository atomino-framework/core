<?php namespace Atomino\RequestPipeline\Responder\Smart;

use Atomino\RequestPipeline\Responder\Smart\Attributes\Args;
use Atomino\RequestPipeline\Responder\Smart\Attributes\Cache;
use Atomino\RequestPipeline\Responder\Smart\Attributes\CSS;
use Atomino\RequestPipeline\Responder\Smart\Attributes\Init;
use Atomino\RequestPipeline\Responder\Smart\Attributes\JS;
use Atomino\RequestPipeline\Responder\Responder;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class SmartResponder extends Responder {

	protected Environment $twig;
	protected ?string $template = null;
	protected \ReflectionClass $ref;

	#[ArrayShape([
		'js'              => 'array',
		'css'             => 'array',
		'frontendVersion' => 'int',
		'class'           => 'string',
		'favicon'         => 'string',
		'language'        => 'string',
		'title'           => 'string',
		'data'            => ParameterBag::class,
	])]
	protected array $smart = [];

	protected function respond(Response $response): Response {
		$this->smart = [
			'js'              => [],
			'css'             => [],
			'forntendVersion' => 0,
			'class'           => null,
			'favicon'         => null,
			'language'        => null,
			'title'           => null,
			'data'            => new ParameterBag(),
		];

		$this->prepare($response);
		$this->setup();

		$smart = $this->smart;
		$smart['data'] = base64_encode(json_encode($this->smart['data']->all()));

		return $response->setContent(
			$this->twig->render(
				$this->template,
				[
					'smartpage' => $smart,
					'viewmodel' => (array)$this,
				]
			)
		);
	}

	abstract protected function prepare(Response $response);

	private function setup() {
		$SMARTRESPONDER = new \ReflectionClass(static::class);

		$Init = Init::get($SMARTRESPONDER);

		$loader = new FilesystemLoader();
		$loader->addPath(__DIR__ . '/@resource', 'smartpage');

		$this->twig = new Environment($loader, [
			'debug'       => $Init->debug,
			'auto_reload' => $Init->debug,
		]);

		$this->smart['frontendVersion'] = $Init->frontendVersion;
		$this->template = $Init->template;

		if (!is_null($Init->twigCache)) $this->twig->setCache($Init->twigCache);
		foreach ($Init->namespaces as $namespace => $path) $loader->addPath($path, $namespace);

		Args::get($SMARTRESPONDER)?->set($this->smart);
		foreach (JS::all($SMARTRESPONDER) as $JS) $JS->set($this->smart);
		foreach (CSS::all($SMARTRESPONDER) as $CSS) $CSS->set($this->smart);

		Cache::get($SMARTRESPONDER);
	}
}






