<?php namespace Atomino\Routing;

use Atomino\Core\Application;
use Atomino\Routing\Interfaces\MiddlewareInterface;
use Atomino\Routing\Interfaces\ResponderInterface;
use Atomino\Routing\Pipeline\BreakPipelineException;
use Atomino\Routing\Pipeline\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Pipeline{

	private array $pipeline = [];

	public function __construct(private Request $request, private ParameterBag $hostBag, private ParameterBag $pathBag, private EventDispatcher $eventDispatcher){ }

	public function clear(){ $this->pipeline = []; }

	public function pipe(string|array $class, $args = []){
		if (is_array($class)) extract($class);
		if (!is_subclass_of($class, ResponderInterface::class)){
			throw new \InvalidArgumentException(sprintf("Pipeline member must implement %s", ResponderInterface::class));
		}
		$this->pipeline[] = ['class' => $class, 'args' => $args];
		return $this;
	}

	public function exec(string|array|null $class = null, $args = []): void{
		if (!is_null($class)) $this->pipe($class, $args);
		try{
			$this->next(new Response())->send();
			die();
		}catch (BreakPipelineException $e){
			$this->clear();
		}
	}

	public function next(Response $response): Response{
		/** @var string $class */
		/** @var array $args */
		/** @var \Atomino\Routing\Interfaces\ResponderInterface $responder */
		if (count($this->pipeline) === 0){
			$this->eventDispatcher->dispatch(new Event($response), Event::onTop);
			return $response;
		}
		extract(array_shift($this->pipeline));
		$responder = Application::DIC()->make($class);
		if ($responder instanceof MiddlewareInterface) $responder->setPipeline($this);
		return $responder->exec($response, $this->request, $this->hostBag, $this->pathBag, new ParameterBag($args));
	}
}