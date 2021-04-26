<?php namespace Atomino\RequestPipeline\Middleware;

use Atomino\RequestPipeline\Pipeline\Handler;
use JetBrains\PhpStorm\Pure;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler extends Handler {
	public function __construct(private Logger $logger){ }

	#[Pure] static public function setup($errorUrl = '/error'){ return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response{
		try{
			$response = $this->next($request);
		}catch (\Throwable $exception){
			$this->logger->error($exception->getMessage(), [$request->getMethod().' '.$request->getSchemeAndHttpHost().$request->getPathInfo().(($q = $request->getQueryString())?"?".$q:'')]);
			return new RedirectResponse($this->arguments->getAlnum('errorUrl', '/error'));
		}
		return $response;
	}
}