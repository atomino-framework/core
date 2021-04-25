<?php namespace Atomino\RequestPipeline\Middleware;

use Atomino\RequestPipeline\Pipeline\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Measure extends Handler {
	public function handle(Request $request): Response{
		$response = $this->next($request);
		$response->headers->set('x-runtime', microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]);
		return $response;
	}
}