<?php namespace Atomino\RequestPipeline\Middleware;

use Atomino\RequestPipeline\Pipeline\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Emitter extends Handler {
	public function handle(Request $request): Response|null {
		$response = $this->next($request);
		if (is_null($response))  $response = new Response("404 Not found", 404);
		$response->send();
		return $response;
	}
}