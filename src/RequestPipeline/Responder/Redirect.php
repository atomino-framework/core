<?php namespace Atomino\RequestPipeline\Responder;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class Redirect extends Basic {
	#[Pure] static public function setup($url = '/', $statuscode = 302, $immediate = true) { return parent::args(get_defined_vars()); }

	protected function respond(Response $response): Response|null {
		$this->redirect($this->arguments->get('url'), $this->arguments->get('statuscode'), $this->arguments->get('immediate'));
	}
}