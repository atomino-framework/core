<?php namespace Atomino\RequestPipeline\Responder;

use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;
use function Atomino\dic;


abstract class Responder extends Basic {

	protected ParameterBag $attributes;
	protected ParameterBag $data;
	protected FileBag $files;
	protected Request $request;
	protected InputBag $query;
	protected InputBag $cookies;
	protected HeaderBag $headers;
	protected ServerBag $server;
	protected false|string|null $content;
	protected InputBag|ParameterBag $post;

	public function handle(Request $request): Response|null {
		$this->initializeHandler($request);
		return $this->respond(dic()->get(Response::class));
	}

	protected function initializeHandler(Request $request) {
		$this->request = $request;
		$this->attributes = $request->attributes;
		$this->files = $request->files;
		$this->post = $request->request;
		$this->query = $request->query;
		$this->cookies = $request->cookies;
		$this->headers = $request->headers;
		$this->server = $request->server;
		$this->content = $request->getContent();
		try {
			$this->data = new ParameterBag($request->toArray());
		} catch (\Exception $e) {
			$this->data = new ParameterBag();
		}
	}

	abstract protected function respond(Response $response): Response|null;
}