<?php namespace Atomino\Responder;

use Atomino\Core\Application;
use Atomino\Responder\Responder;
use Atomino\Responder\SmartResponder\Attributes\Args;
use Atomino\Responder\SmartResponder\Attributes\CSS;
use Atomino\Responder\SmartResponder\Attributes\JS;
use Atomino\Responder\SmartResponder\Attributes\SmartEnv;
use Atomino\Responder\SmartResponder\Attributes\Template;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


abstract class SmartResponder extends Responder{

	protected Environment $twig;
	protected ?string $template = null;
	protected \ReflectionClass $ref;
	protected array $args = ['js' => [], 'css' => []];
	protected array $data = [];
	protected string $frontendRoot = '';
	protected int $frontendVersion = 0;

	protected function respond(Response $response): Response{
		$this->prepare($response);
		$this->setup();
		return $response->setContent($this->twig->render($this->template, ['smartpage' => array_merge($this->args, ['data' => base64_encode(json_encode($this->data))]), 'viewmodel' => (array)$this]));
	}

	abstract protected function prepare(Response $responder);

	private function setup(){
		$loader = new FilesystemLoader();
		$loader->addPath(__DIR__ . '/SmartResponder/@resource', 'smartpage');
		$this->twig = new Environment($loader, [
			'debug'       => Application::ENV()->isDev(),
			'auto_reload' => !Application::ENV()->isDev(),
		]);

		foreach (( new \ReflectionClass($this) )->getAttributes() as $attribute){
			$instance = $attribute->newInstance();
			if ($instance instanceof SmartEnv){
				$env = $instance->getEnv();
				if (!is_null($env['twigCache'])) $this->twig->setCache($env['twigCache']);
				foreach ($env['namespaces'] as $namespace => $path) $loader->addPath($path, $namespace);
				$this->frontendVersion = $env['frontendVersion'];
				$this->frontendRoot = $env['frontendRoot'];
			}
			if ($instance instanceof Template) $instance->set($this->template);
			if ($instance instanceof Args) $instance->set($this->args);
			if ($instance instanceof JS) $instance->set($this->args);
			if ($instance instanceof CSS) $instance->set($this->args);
		}

	}
}






