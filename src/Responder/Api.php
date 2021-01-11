<?php namespace Atomino\Responder;

use Atomino\Core\Application;
use Atomino\Responder\Api\Attributes\Middleware;
use Atomino\Responder\Api\Attributes\Route;
use Atomino\Routing\Matcher;
use Atomino\Routing\Pipeline\Event;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;


abstract class Api extends \Atomino\Responder\Middleware{

	#[Pure] public static function setup(string $apiBase): array{ return parent::args(get_defined_vars()); }

	const HEAD = 'HEAD';
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const DELETE = 'DELETE';
	const PURGE = 'PURGE';
	const OPTIONS = 'OPTIONS';
	const TRACE = 'TRACE';
	const CONNECT = 'CONNECT';

	public function respond(Response $response): Response{
		$matcher = Application::DIC()->get(Matcher::class);
		$methods = ( new \ReflectionClass(static::class) )->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method){
			if ($attributes = $method->getAttributes(Route::class)){
				/** @var Route $routeInstance */
				$routeInstance = $attributes[0]->newInstance();
				if (empty($routeInstance->verb) || in_array($this->getRequest()->getMethod(), $routeInstance->verb)){
					$argumentsBag = new ParameterBag();
					$pattern = $routeInstance->getPattern($method, $this->getArgsBag()->get('apiBase'));
					if ($matcher($pattern, $this->getRequest()->getPathInfo(), '/', $argumentsBag)){

						if ($middlewareAttributes = $method->getAttributes(Middleware::class)){
							foreach ($middlewareAttributes as $middlewareAttribute){
								/** @var Middleware $middleware */
								$middleware = $middlewareAttribute->newInstance();
								$this->pipe($middleware->class, $middleware->args);
							}
						}

						Application::DIC()->get(EventDispatcher::class)->addListener(Event::onTop, function (Event $event) use ($method, $argumentsBag){
							$result = $method->invoke($this, ...$argumentsBag->all());
							$event->response->headers->set('Content-Type', 'application/json');
							$event->response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));
						});

						return $this->next($response);
					}
				}
			}
		}
		return $response->setStatusCode(404);
	}
}