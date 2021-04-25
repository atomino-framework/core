<?php namespace Atomino\RequestPipeline\FileServer;

use Atomino\RequestPipeline\Pipeline\Handler;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileLocator extends Handler {

	#[Pure] static public function setup($path): array { return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response|null {
		$file = realpath($this->arguments->get('path') . $request->getPathInfo());
		$request->attributes->set('file', $file);
		return $this->next($request);
	}
}