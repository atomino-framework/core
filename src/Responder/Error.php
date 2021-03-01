<?php namespace Atomino\Responder;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;


class Error extends Responder{
	#[Pure] static public function setup($statuscode = 404){ return parent::args(get_defined_vars()); }
	final public function respond(Response $response): Response{
		return $response->setStatusCode($this->getArgsBag()->get('statuscode', 404));
	}
}