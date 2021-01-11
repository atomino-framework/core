<?php namespace Atomino\Responder\FileServer;

use Atomino\Responder\Middleware;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class FileLocatorMiddleware extends Middleware{

	#[Pure] static public function setup($path, $breakOnError = false){ return parent::args(get_defined_vars()); }

	protected function respond(Response $response): Response{
		$file = realpath($this->getArgsBag()->get('path') . $this->getPathBag()->get('__REST'));
		if ((!$file || !file_exists($file)) && $this->getArgsBag()->get('breakOnError')) $this->break();
		$this->getAttributesBag()->set('file', $file);
		return $this->next($response);
	}
}