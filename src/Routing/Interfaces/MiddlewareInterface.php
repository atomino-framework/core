<?php namespace Atomino\Routing\Interfaces;

use Atomino\Routing\Pipeline;
use Atomino\Routing\Interfaces\ResponderInterface;


interface MiddlewareInterface extends ResponderInterface{
	public function setPipeline(Pipeline $pipeline): void;
}