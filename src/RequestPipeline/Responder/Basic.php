<?php namespace Atomino\RequestPipeline\Responder;

use Atomino\RequestPipeline\Pipeline\Handler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Atomino\dic;


abstract class Basic extends Handler {

	protected Request $request;

	public function handle(Request $request): Response|null {
		$this->request = $request;
		return $this->respond(dic()->get(Response::class));
	}

	protected final function redirect($url = '/', $statusCode = 302, $immediate = true): Response {
		$response = new RedirectResponse($url, $statusCode);
		if ($immediate) {
			$response->send();
			die();
		}
		return $response;
	}

	abstract protected function respond(Response $response): Response|null;
}