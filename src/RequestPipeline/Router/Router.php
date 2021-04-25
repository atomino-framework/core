<?php namespace Atomino\RequestPipeline\Router;


use Atomino\RequestPipeline\Pipeline\Handler;
use Atomino\RequestPipeline\Router\Matcher\HostMatcher;
use Atomino\RequestPipeline\Router\Matcher\PathMatcher;
use Atomino\RequestPipeline\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Atomino\inject;

abstract class Router extends Handler {

	private Pipeline|null $subPipeLine = null;
	protected Request $request;

	public function handle(Request $request): Response|null {
		$this->request = $request;
		$this->route();
		if (is_null($this->subPipeLine)) return $this->next($request);
		else return $this->subPipeLine->execute();
	}

	abstract protected function route(): void;

	public function __invoke($method = null, $path = null, $host = null, $port = null, $scheme = null): Pipeline|null {
		if (is_null($this->subPipeLine)) {
			$request = $this->request;
			if (
				(is_null($method) || $method === $this->request->getMethod()) &&
				(is_null($port) || $port == $this->request->getPort()) &&
				(is_null($scheme) || $scheme === $this->request->getScheme()) &&
				(is_null($path) || (new PathMatcher($path, $request))->isMatches()) &&
				(is_null($host) || (new HostMatcher($host, $request))->isMatches())
			) {
				$this->subPipeLine = new Pipeline($request);
			}
		}
		return $this->subPipeLine;
	}

	static public function create(callable $route): Router {
		$router = new class extends Router {
			protected \Closure $routing;
			protected function route(): void { ($this->routing)($this); }
		};
		inject($router, 'routing', \Closure::bind($route, $router, get_class($router)));
		return $router;

	}
}