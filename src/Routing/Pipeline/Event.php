<?php namespace Atomino\Routing\Pipeline;

use Symfony\Component\HttpFoundation\Response;


class Event extends \Symfony\Contracts\EventDispatcher\Event{
	const onTop = __CLASS__ . '.onTop';
	public function __construct(public Response $response){ }
}