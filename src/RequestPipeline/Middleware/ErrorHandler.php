<?php namespace Atomino\RequestPipeline\Middleware;

use Atomino\RequestPipeline\Pipeline\Handler;
use Atomino\RequestPipeline\Pipeline\Pipeline;
use JetBrains\PhpStorm\Pure;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandler extends Handler {
	public function __construct(private Logger $logger) { }

	#[Pure] static public function setup(array|string $nullHandler, array $statusHandlers = []) { return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response {
		$response = $this->next($request);
		$handler = null;

		if (!is_null($response) && $response->getStatusCode() !== 200) {
			if (array_key_exists($response->getStatusCode(), $this->arguments->get('statusHandlers'))) {
				$statusHandler = $this->arguments->get('statusHandlers')[$response->getStatusCode()];
				if(!is_array($statusHandler)) $statusHandler = [$statusHandler];
				/** @var Handler $handler */
				$handler = Pipeline::create(function (Pipeline $pipeline) use ($response, $statusHandler) {
					$pipeline->pipe(...$statusHandler);
				});
			} else {
				$response = null;
			}
		}

		if (is_null($response)) {
			/** @var Handler $handler */
			$nullHandler = $this->arguments->get('nullHandler');
			if(!is_array($nullHandler)) $nullHandler = [$nullHandler];
			$handler = Pipeline::create(function (Pipeline $pipeline) use ($nullHandler) {
				$pipeline->pipe(...$nullHandler);
			});
		}

		if (!is_null($handler)) {
			$this->logger->error("Error " . ($response ? $response->getStatusCode() : 'NULL'), [$request->getMethod() . ' ' . $request->getSchemeAndHttpHost() . $request->getPathInfo() . (($q = $request->getQueryString()) ? "?" . $q : '')]);
			$response = $handler->handle($request);
		}
		return $response;
	}
}