<?php namespace Atomino\RequestPipeline\Pipeline;

use Symfony\Component\HttpFoundation\ParameterBag;
use function Atomino\dic;
use function Atomino\inject;

class Segment {
	public function __construct(private $handler, private array|null $arguments, private Pipeline $pipeLine) { }
	public function getHandler(): Handler {
		$handler = is_string($this->handler) ? dic()->get($this->handler) : $this->handler;
		if (!($handler instanceof Handler)) throw new \Exception("Pipeline handler must be a Handler dude!");
		inject($handler, 'arguments', new ParameterBag($this->arguments ?? []));
		inject($handler, 'pipeLine', $this->pipeLine);
		return $handler;
	}
}