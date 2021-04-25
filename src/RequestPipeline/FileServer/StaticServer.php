<?php namespace Atomino\RequestPipeline\FileServer;



use Atomino\RequestPipeline\Router\Router;

class StaticServer {
	public static function route(Router $router, string $pattern, string $path){
		$router(method: 'GET', path: $pattern)
			?->pipe(FileLocator::setup($path))
		     ->pipe(FileServer::class);
	}
}