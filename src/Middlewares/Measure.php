<?php namespace Atomino\Middlewares;

use Atomino\Responder\Middleware;
use Symfony\Component\HttpFoundation\Response;


class Measure extends Middleware{

	public function respond(Response $response): Response{
		$response = $this->next($response);
		$response->headers->set('runtime', microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]);
		return $response;
	}
}