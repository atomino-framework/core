<?php namespace Atomino\Responder;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;


class Redirect extends Responder{
	#[Pure] static public function setup($url = '/', $statuscode = 302, $immediate = true){ return parent::args(get_defined_vars()); }
	final public function respond(Response $response): Response{
		return $this->redirect(
			$response,
			$this->getArgsBag()->get('url'),
			$this->getArgsBag()->get('statuscode'),
			$this->getArgsBag()->get('immediate')
		);
	}
}