<?php namespace Atomino\Responder;

use Atomino\Routing\Interfaces\MiddlewareInterface;
use Atomino\Routing\Pipeline;
use Symfony\Component\HttpFoundation\Response;


abstract class Middleware extends Responder implements MiddlewareInterface{

	private Pipeline $pipeline;
	public function setPipeline(Pipeline $pipeline): void{ $this->pipeline = $pipeline; }
	public function pipe(string $class, $arguments = []){ $this->pipeline->pipe($class, $arguments); }
	protected final function next(Response $response): Response{ return $this->pipeline->next($response); }

}