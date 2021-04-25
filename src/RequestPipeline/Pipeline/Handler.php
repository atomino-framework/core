<?php namespace Atomino\RequestPipeline\Pipeline;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Handler {
	#[Pure] static protected final function args($args) { return ([get_called_class(), $args]); }

	protected ParameterBag $arguments;
	protected Nextable $pipeLine;
	abstract public function handle(Request $request): Response|null;
	protected function next(Request $request): Response|null { return $this->pipeLine->next($request); }
}