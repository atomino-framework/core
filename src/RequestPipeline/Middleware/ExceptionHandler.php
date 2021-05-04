<?php namespace Atomino\RequestPipeline\Middleware;

use Atomino\RequestPipeline\Pipeline\Handler;
use Atomino\RequestPipeline\Pipeline\Pipeline;
use JetBrains\PhpStorm\Pure;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler extends Handler {
	public function __construct(private Logger $logger) { }

	#[Pure] static public function setup(array|string $exceptionHandler) { return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response {
		try {
			$response = $this->next($request);
		} catch (\Throwable $exception) {
			$this->logger->error($exception->getMessage(), [$request->getMethod() . ' ' . $request->getSchemeAndHttpHost() . $request->getPathInfo() . (($q = $request->getQueryString()) ? "?" . $q : '')]);
			$exceptionHandler = $this->arguments->get('exceptionHandler');
			if (!is_array($exceptionHandler)) $exceptionHandler = [$exceptionHandler];
			$handler = Pipeline::create(function (Pipeline $pipeline) use ($exceptionHandler) { $pipeline->pipe(...$exceptionHandler); });
			$response = $handler->next($request);
		}
		return $response;
	}
}