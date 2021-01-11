<?php namespace Atomino\Routing;

use Atomino\Core\Application;
use Atomino\Core\Runner\HttpRunnerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class Router implements HttpRunnerInterface{

	private ParameterBag $hostBag;
	private ParameterBag $pathBag;
	private Pipeline $pipeline;
	public function getRequest(): Request{ return $this->request; }
	public function getPathBag(): ParameterBag{ return $this->pathBag; }
	public function getHostBag(): ParameterBag{ return $this->hostBag; }
	public function getPipeline(): Pipeline{ return $this->pipeline; }

	public function __construct(private Request $request, private Matcher $matcher){
		$this->hostBag = new ParameterBag();
		$this->pathBag = new ParameterBag();
		$this->pipeline = Application::DIC()->make(Pipeline::class, ['request' => $this->request, 'hostBag' => $this->hostBag, 'pathBag' => $this->pathBag]);
	}

	public function __invoke($method = null, $path = null, $host = null, $port = null, $scheme = null):static|null{
		return (
			( is_null($method) || $method === $this->request->getMethod() ) &&
			( is_null($port) || $port == $this->request->getPort() ) &&
			( is_null($scheme) || $scheme === $this->request->getScheme() ) &&
			( is_null($path) || ( $this->matcher )($path, $this->request->getPathInfo(), '/', $this->pathBag) ) &&
			( is_null($host) || ( $this->matcher )($host, $this->request->getHost(), '.', $this->hostBag) )
		) ? $this : null;
	}

	public function test(callable $test):static|null{ return $test() === true ? $this : null; }

	public function pass(HttpRunnerInterface|string|callable $runner):void{
		if (is_callable($runner)){
			$runner();
		}else{
			if (is_string($runner)) $runner = Application::DIC()->get($runner);
			if (!$runner instanceof HttpRunnerInterface) throw new \InvalidArgumentException();
			$runner->run();
		}
	}

	public function pipe(string|array $class, array $args = []): Pipeline{
		$this->pipeline->pipe($class, $args);
		return $this->pipeline;
	}

	public function clearPipeline(): static{
		$this->pipeline->clear();
		return $this;
	}

	public function exec(string|array|null $class = null, array $args = []):void{
		$this->pipeline->exec($class, $args);
	}

}