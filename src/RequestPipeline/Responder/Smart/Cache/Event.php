<?php namespace Atomino\RequestPipeline\Responder\Smart\Cache;

class Event extends \Symfony\Contracts\EventDispatcher\Event{
	const request = __CLASS__.'.request';
	public function __construct(public int $interval){}
}