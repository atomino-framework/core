<?php namespace Atomino\Routing;

use Atomino\Core\Application;
use Atomino\Core\Runner\HttpRunnerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class Router implements HttpRunnerInterface {

	private ParameterBag $hostBag;
	private ParameterBag $pathBag;
	private Request $request;
	private Matcher $matcher;
	private array $pipeline = [];
	public function getRequest(): Request { return $this->request; }
	public function getPathBag(): ParameterBag { return $this->pathBag; }
	public function getHostBag(): ParameterBag { return $this->hostBag; }

	public function __construct() {
		$this->request = Application::DIC()->get(Request::class);
		$this->matcher = Application::DIC()->get(Matcher::class);
		$this->hostBag = new ParameterBag();
		$this->pathBag = new ParameterBag();
	}

	public function __invoke($method = null, $path = null, $host = null, $port = null, $scheme = null): Pipeline|null {
		if (
			(is_null($method) || $method === $this->request->getMethod()) &&
			(is_null($port) || $port == $this->request->getPort()) &&
			(is_null($scheme) || $scheme === $this->request->getScheme()) &&
			(is_null($path) || ($this->matcher)($path, $this->request->getPathInfo(), '/', $this->pathBag)) &&
			(is_null($host) || ($this->matcher)($host, $this->request->getHost(), '.', $this->hostBag))
		) {
			$pipeline = Application::DIC()->make(Pipeline::class, ['request' => $this->request, 'hostBag' => $this->hostBag, 'pathBag' => $this->pathBag]);
			foreach ($this->pipeline as $segment) $pipeline->pipe($segment['class'], $segment['args']);
			return $pipeline;
		} else {
			return null;
		}
	}

	public function pipe(string|array $class, array $args = []): Router {
		$this->pipeline[] = ['class' => $class, 'args' => $args];
		return $this;
	}

	public function clear(): static {
		$this->pipeline = [];
		return $this;
	}

}